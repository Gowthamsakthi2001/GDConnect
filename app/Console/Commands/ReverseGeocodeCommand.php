<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeocodingService;

class ReverseGeocodeCommand extends Command
{
    protected $signature = 'app:reverse-geocode-command';
    protected $description = 'Reverse geocode punch-in and punch-out locations';

    protected GeocodingService $geoService;

    public function __construct(GeocodingService $geoService)
    {
        parent::__construct();
        $this->geoService = $geoService;
    }
    
    public function handle()
    {
        $this->info('Reverse geocoding started...');
        Log::channel('reverse_geocode')->info('Command started');
    
        $updatedCount = 0;
    
        DB::table('ev_delivery_man_logs')
            // ONLY rows where punch-in address is missing
            ->whereNull('punchin_address')
            ->orderByDesc('id')
            ->chunkById(50, function ($logs) use (&$updatedCount) {
    
                foreach ($logs as $log) {
    
                    Log::channel('reverse_geocode')->info('Processing ID', [
                        'id' => $log->id
                    ]);
    
                    try {
                        $update = [];
    
                        /**
                         * PUNCH-IN
                         * Lat & Long are guaranteed
                         */
                        if (
                            is_null($log->punchin_address) &&
                            !empty($log->punchin_latitude) &&
                            !empty($log->punchin_longitude)
                        ) {
                            $update['punchin_address'] = $this->geoService->reverse(
                                $log->punchin_latitude,
                                $log->punchin_longitude
                            );
                        }
    
                        /**
                         * PUNCH-OUT
                         * Lat & Long are optional
                         * Update ONLY if they exist
                         */
                        if (
                            is_null($log->punchout_address) &&
                            !empty($log->punchout_latitude) &&
                            !empty($log->punchout_longitude)
                        ) {
                            $update['punchout_address'] = $this->geoService->reverse(
                                $log->punchout_latitude,
                                $log->punchout_longitude
                            );
                        }
    
                        // Perform update only when needed
                        if (!empty($update)) {
    
                            DB::table('ev_delivery_man_logs')
                                ->where('id', $log->id)
                                ->update($update);
    
                            $updatedCount++;
    
                            Log::channel('reverse_geocode')->info('Updated', [
                                'id'     => $log->id,
                                'fields' => array_keys($update)
                            ]);
                        }
    
                        // API rate limiting safety
                        sleep(1);
    
                    } catch (\Throwable $e) {
    
                        Log::channel('reverse_geocode')->error('Reverse geocode failed', [
                            'id'    => $log->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });
    
        $this->info("Reverse geocoding completed. Updated rows: {$updatedCount}");
        Log::channel('reverse_geocode')->info('Command completed', [
            'updated_count' => $updatedCount
        ]);
    }

}
