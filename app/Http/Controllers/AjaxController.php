<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function getLastPurchasePriceRM(Request $request)
    {

        $data = DB::table('stock_inward_mst as a')
            ->select("b.price")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->where("a.vendor_id", $request->vendor_id)
            ->where("b.product_id", $request->product_id)
            ->orderBy("b.id", "desc")
            ->first()
            ?? DB::table("products")
            ->select("price")
            ->where("id", $request->product_id)
            ->first();

        return $data?->price;
    }
}
