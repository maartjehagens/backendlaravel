<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model {
  use Searchable;
  public function toSearchableArray(): array {
    return [
      'id'=>$this->id,'name'=>$this->name,
      'description'=>$this->description,
      'price'=>(float)$this->price,'is_active'=>(bool)$this->is_active,
    ];
  }
}


