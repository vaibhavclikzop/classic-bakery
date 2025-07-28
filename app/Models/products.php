<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $primaryKey = "id";
    public function unitType()
    {
        return $this->belongsTo(unit_type::class, "uom", "id");
    }
}
