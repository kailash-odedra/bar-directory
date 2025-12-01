<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedRouteKey
{
    /**
     * Get the value of the model's route key.
     * This encrypts the ID when generating URLs
     */
    public function getRouteKey()
    {
        return Crypt::encryptString($this->getKey());
    }

    /**
     * Retrieve the model for bound value.
     * This decrypts the ID when resolving route binding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            // The value might be URL encoded, try decoding it first
            $decodedValue = urldecode($value);
            
            // Try to decrypt
            try {
                $decryptedId = Crypt::decryptString($decodedValue);
            } catch (\Exception $e) {
                // If that fails, try without URL decoding (in case Laravel already decoded it)
                $decryptedId = Crypt::decryptString($value);
            }
            
            $field = $field ?? $this->getRouteKeyName();
            
            $model = $this->where($field, $decryptedId)->first();
            
            if (!$model) {
                return null;
            }
            
            return $model;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Decryption failed - invalid encrypted string
            \Log::error('Route binding decryption failed: ' . $e->getMessage(), ['value' => $value]);
            return null;
        } catch (\Exception $e) {
            // Other errors
            \Log::error('Route binding error: ' . $e->getMessage(), ['value' => $value]);
            return null;
        }
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}

