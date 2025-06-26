<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock_inward_mst_finish_goods extends Model
{
    use HasFactory;
    protected $table="stock_inward_mst_finish_goods";
    protected $primaryKey="id";
    public function vendorDetails(){
        return $this->belongsTo(vendor::class,"vendor_id","id");
    }
    public function poDetails(){
        return $this->belongsTo(po_mst_finish_goods::class,"po_id","id");
    }
}
