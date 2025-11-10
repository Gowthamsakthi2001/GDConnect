<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    /**
     * @var array{
     *   images?: string[]|string|null,  // absolute temp paths (single or array)
     *   video?: string|null             // absolute temp path
     * }
     */
    protected array $temp;

    /** e.g. "b2b/recovery_comments" (under public/) */
    protected string $publicDir;

    /**
     * Optional model update config:
     * [
     *   'model_class'   => \App\Models\RecoveryComment::class,
     *   'model_id'      => 123,
     *   'images_attr'   => 'images',   // JSON/array or text column
     *   'video_attr'    => 'video',    // string/text column
     *   // 'store_full_url' => true,    // optional; default false
     * ]
     */
    protected ?array $modelUpdate;

    /** Track whether images input was array so we decide how to store (string vs array) */
    protected bool $imagesWasArrayInput = false;

    public function __construct(array $temp, string $publicDir, ?array $modelUpdate = null)
    {
        // Remember original shape (string vs array) to decide DB storage type later
        $this->imagesWasArrayInput = is_array($temp['images'] ?? null);

        // Normalize for processing (always process as array internally)
        if (isset($temp['images']) && !is_array($temp['images']) && !is_null($temp['images'])) {
            $temp['images'] = [$temp['images']];
        }

        $this->temp        = $temp;
        $this->publicDir   = trim($publicDir, '/');
        $this->modelUpdate = $modelUpdate;
    }

    public function handle(): void
    {
        $destPath = public_path($this->publicDir);
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0777, true);
        }

        $savedImages = $this->moveImages($destPath, $this->temp['images'] ?? []);
        $savedVideo  = $this->moveVideo($destPath, $this->temp['video'] ?? null);

        Log::info('UploadMediaJob completed', [
            'dir'             => $this->publicDir,
            'images'          => $savedImages,
            'video'           => $savedVideo,
            'imagesWasArray'  => $this->imagesWasArrayInput,
        ]);

        if ($this->modelUpdate) {
            $this->updateModel($savedImages, $savedVideo);
        }
    }

    /** @return string[] filenames or full URLs (depending on store_full_url) */
    protected function moveImages(string $destPath, array $images): array
    {
        $out = [];
        foreach ($images as $tmp) {
            $saved = $this->moveOne($tmp, $destPath, 'img_', 'jpg');
            if ($saved) $out[] = $this->formatOut($saved);
        }
        return $out;
    }

    /** @return string|null filename or full URL */
    protected function moveVideo(string $destPath, ?string $tmp): ?string
    {
        if (!$tmp) return null;
        $saved = $this->moveOne($tmp, $destPath, 'vid_', 'mp4');
        return $saved ? $this->formatOut($saved) : null;
    }

    /** Move a single temp file → public dir, return filename (without path) */
    protected function moveOne(string $tmpPath, string $destPath, string $prefix, string $fallbackExt): ?string
    {
        if (!$tmpPath || !File::exists($tmpPath)) {
            Log::warning('Temp file missing', ['path' => $tmpPath]);
            return null;
        }
        $ext = pathinfo($tmpPath, PATHINFO_EXTENSION) ?: $fallbackExt;
        $filename = uniqid($prefix) . '_' . Str::random(6) . '.' . $ext;

        // Move file
        File::move($tmpPath, $destPath . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    /** Optionally convert filename → full URL */
    protected function formatOut(string $filename): string
    {
        $full = $this->modelUpdate['store_full_url'] ?? false;
        return $full ? url($this->publicDir . '/' . $filename) : $filename;
    }

    protected function updateModel(array $savedImages, ?string $savedVideo): void
    {
        try {
            /** @var class-string<Model> $class */
            $class = $this->modelUpdate['model_class'] ?? null;
            $id    = $this->modelUpdate['model_id']    ?? null;
            if (!$class || !$id) return;

            /** @var Model $model */
            $model = $class::query()->find($id);
            if (!$model) return;

            // IMAGES
            if (!empty($this->modelUpdate['images_attr'])) {
                $attr = $this->modelUpdate['images_attr'];

                if ($this->imagesWasArrayInput) {
                    // Treat as multiple (even if count = 1). Merge into an array.
                    $existing = [];
                    if (!empty($model->{$attr})) {
                        $existing = is_string($model->{$attr})
                            ? (json_decode($model->{$attr}, true) ?: [$model->{$attr}]) // string → make array
                            : (is_array($model->{$attr}) ? $model->{$attr} : []);
                    }
                    $merged = array_values(array_filter(array_merge($existing, $savedImages)));

                    // assign array
                    $model->{$attr} = $merged;

                    // If the column isn't cast to array/json, make sure we store JSON string
                    if (!is_array($model->{$attr})) {
                        $model->{$attr} = json_encode($merged);
                    }
                } else {
                    // Single image input → store as plain string
                    $single = $savedImages[0] ?? null;
                    if ($single !== null) {
                        $model->{$attr} = $single; // string
                    } else {
                        // nothing saved; do not modify
                    }
                }
            }

            // VIDEO (always single → string)
            if (!empty($this->modelUpdate['video_attr']) && $savedVideo) {
                $model->{$this->modelUpdate['video_attr']} = $savedVideo; // string
            }

            $model->save();
        } catch (\Throwable $e) {
            Log::error('UploadMediaJob model update failed', ['error' => $e->getMessage()]);
        }
    }
}
