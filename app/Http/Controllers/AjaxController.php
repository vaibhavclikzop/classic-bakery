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
    public function getLastPurchasePriceFG(Request $request)
    {

        $data = DB::table('stock_inward_det_finish_goods as a')
            ->select("a.price")
            ->where("a.product_id", $request->product_id)
            ->orderBy("a.id", "desc")
            ->first()
            ?? DB::table("finish_products_mst")
            ->select("price")
            ->where("id", $request->product_id)
            ->first();

        return $data?->price;
    }

    public function getRMorTradingItems(Request $request)
    {
        if ($request->type == "raw_material") {
            return  DB::table("products as a")
                ->select("a.*", DB::raw("CASE WHEN b.stock > 0 THEN b.stock ELSE 0 END as stock"))
                ->leftJoin("current_stock as b", "a.id", "b.product_id")
                ->get();
        } else {
            return DB::table("finish_products_mst as a")
                ->select("a.*", DB::raw("CASE WHEN b.stock > 0 THEN b.stock ELSE 0 END as stock"))
                ->leftJoin("finish_product_stock as b", "a.id", "b.product_id")
                ->where("a.f_category_id", 2)->get();
        }
    }
}
