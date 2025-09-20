<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outlet_customer_order_mst extends Model
{
    use HasFactory;
    protected $table = "outlet_customer_order_mst";
    protected $primaryKey = "id";

    public function customerDetails()
    {
        return $this->belongsTo(outlet_customers::class, "outlet_customer_id", "id");
    }
    public function outletDetails()
    {
        return $this->belongsTo(outlet::class, "outlet_id", "id");
    }

    public function productDetails()
    {
        return $this->hasMany(outlet_customer_order_det::class, "mst_id", "id");
    }
}
