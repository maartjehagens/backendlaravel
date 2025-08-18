<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    // Optioneel: eigen indexnaam
    public function searchableAs(): string
    {
        return 'products';
    }

    // Welke velden worden geïndexeerd
    public function toSearchableArray(): array
    {
        return [
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'description' => (string) ($this->description ?? ''),
            'price'       => (float) $this->price,
            'is_active'   => (bool) $this->is_active,
        ];
    }
}

