<?php 

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QualityCheckBulkImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $name = $row['name'] ?? null;
        $url  = $row['url'] ?? null;

        if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                // Generate direct download URL (for Google Drive)
                $downloadUrl = $this->generateDownloadUrl($url);

                // Attempt to download content
                $fileContent = @file_get_contents($downloadUrl);

                if (!$fileContent || str_starts_with(trim($fileContent), '<!DOCTYPE html')) {
                    Log::error("File content invalid or blocked for: $downloadUrl");
                    return;
                }

                // Detect MIME type from file content
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($fileContent);
                
             
                

                // Determine extension based on MIME
                $extension = match ($mimeType) {
                    'application/pdf' => 'pdf',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'application/msword' => 'doc',
                    default => 'bin',
                };

                // Generate unique filename
                $filename = Str::uuid() . '.' . $extension;
                
                   
                $destination = public_path('EV/images/fc_attachment/');

                // Ensure folder exists
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }

                // Save file
                file_put_contents($destination . $filename, $fileContent);
                Log::info("Downloaded file: $filename from $url");

            } catch (\Exception $e) {
                Log::error("Exception while downloading from $url - " . $e->getMessage());
            }
        } else {
            Log::warning("Invalid or empty URL: " . json_encode($url));
        }
    }

    private function generateDownloadUrl($url)
    {
        // Convert Google Drive view links to direct download links
        if (strpos($url, 'drive.google.com') !== false) {
            if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            }
        }

        // Default: return the original URL
        return $url;
    }
}
