<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SegmentHelper
{
    /**
     * Safely unserialize segment data, handling 'N;' values
     */
    public static function safeUnserialize($data)
    {
        // Handle null or empty values
        if (empty($data) || $data === null) {
            return [];
        }
        
        // Handle 'N;' which represents NULL in some serialization
        if ($data === 'N;') {
            return [];
        }
        
        // Try to unserialize normally
        $result = @unserialize($data);
        
        // If unserialize fails or returns false, return empty array
        if ($result === false || $result === null) {
            Log::warning('SegmentHelper - Failed to unserialize data', [
                'data' => $data
            ]);
            return [];
        }
        
        // Ensure we return an array
        return is_array($result) ? $result : [];
    }
    
    /**
     * Check if unserialized data has valid values
     */
    public static function hasValidData($data)
    {
        $unserialized = self::safeUnserialize($data);
        $hasValid = !empty($unserialized) && isset($unserialized[0]) && $unserialized[0] !== null;
        
        Log::debug('SegmentHelper - Checking valid data', [
            'original_data' => $data,
            'unserialized' => $unserialized,
            'has_valid' => $hasValid
        ]);
        
        return $hasValid;
    }
}