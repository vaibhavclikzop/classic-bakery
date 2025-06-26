<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock_inward_det_finish_goods extends Model
{
    use HasFactory;
    protected $table = "stock_inward_det_finish_goods";
    protected $primaryKey = "id";
    public function productDetails()
    {
        return $this->belongsTo(finish_products_mst::class, "product_id", "id");
    }
}
