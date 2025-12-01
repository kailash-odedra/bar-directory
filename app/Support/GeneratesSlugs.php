<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesSlugs
{
    /**
     * Generate a unique slug for the given model.
     */
    protected function makeUniqueSlug(?string $candidate, string $fallback, string $modelClass, ?int $ignoreId = null): string
    {
        /** @var Model $modelClass */
        $base = Str::slug($candidate ?: $fallback);

        if (! $base) {
            $base = (string) Str::uuid();
        }

        $slug = $base;
        $counter = 1;

        while ($modelClass::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}

