<?php

namespace App\Support\Concerns;

use App\Support\IdHasher;

/**
 * Adds hashed route keys while still accepting numeric values for compatibility.
 */
trait UsesHashids
{
    /**
     * Disable hashids on a per-model basis by setting `$useHashids = false;`
     */
    public bool $useHashids = true;

    public function getRouteKey(): mixed
    {
        if (property_exists($this, 'useHashids') && $this->useHashids === false) {
            return parent::getRouteKey();
        }

        $key = $this->getKey();
        return $key ? IdHasher::encode($key) : parent::getRouteKey();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (property_exists($this, 'useHashids') && $this->useHashids === false) {
            return parent::resolveRouteBinding($value, $field);
        }

        $decoded = IdHasher::decode($value);
        if ($decoded === null) {
            return null;
        }

        return $this->where($field ?? $this->getRouteKeyName(), $decoded)->first();
    }

    /** Accessor: `$model->hashid` returns hashed ID. */
    public function getHashidAttribute(): ?string
    {
        if (property_exists($this, 'useHashids') && $this->useHashids === false) {
            return null;
        }

        $key = $this->getKey();
        return $key ? IdHasher::encode($key) : null;
    }
}
