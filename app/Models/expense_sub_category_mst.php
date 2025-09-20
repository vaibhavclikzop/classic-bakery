<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expense_sub_category_mst extends Model
{
    use HasFactory;
    protected $table = "expense_sub_category_mst";
    protected $primaryKey = "id";

      public function categoryDetails(){
        return $this->belongsTo(expense_category_mst::class,"category_id","id");
    }
}
