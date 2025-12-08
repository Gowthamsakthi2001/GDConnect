<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ImportErrorCollector
{
    private static $errors = [];

    // Track update success/failure
    public static $updatedChassis = [];
    public static $failedChassis = [];

    /**
     * Add Import Error
     */
    public static function add($row, $chassis, $error)
    {
        if (count(self::$errors) >= 50000) {
            return; // Safety limit
        }

        self::$errors[] = [
            'row'            => $row,
            'chassis_number' => $chassis,
            'error_message'  => $error,
        ];

        Log::error("IMPORT ERROR", [
            'row'     => $row,
            'chassis' => $chassis,
            'error'   => $error
        ]);
    }

    /**
     * Return all collected errors
     */
    public static function all()
    {
        return self::$errors;
    }

    /**
     * Reset all stored errors + update results
     */
    public static function clear()
    {
        self::$errors = [];
        self::$updatedChassis = [];
        self::$failedChassis = [];
    }
}
