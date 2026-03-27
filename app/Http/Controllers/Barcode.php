<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Barcode extends Controller
{


    public function index(Request $request)
    {
        $delivery_date = request("delivery_date");
        $product_id = request("product_id");
        $from_order = request("from_order");
        $sub_category_id = request("sub_category_id");
        $category_id = request("category_id");

        $productNames = array();


        $data =  DB::table("finish_products_mst")->get();
        $f_product_category = DB::table("f_product_category")->get();

        if ($delivery_date) {
            if ($from_order == 'add') {
                $mst = DB::table("order_mst as a")
                    ->select(
                        "c.id as product_id",
                        "c.name as product_name",
                        "a.delivery_date",
                        "c.warranty_days as expiry",

                        DB::raw("SUM(b.qty) as qty"),
                        'c.f_category_id as category_id',
                    )
                    ->join("order_det as b", "a.id", "b.mst_id")
                    ->join("finish_products_mst as c", "b.product_id", "c.id")
                    ->whereDate("a.delivery_date", "=", $delivery_date)
                    ->where("a.status", "!=", "cancel");
                if ($product_id) {
                    $mst->where("b.product_id", "=", $product_id);
                }
                if ($category_id) {
                    $mst->where("c.f_category_id", "=", $category_id);
                }
                if ($sub_category_id) {

                    $mst->where("c.f_sub_category_id", "=", $sub_category_id);
                }

                $productNames = $mst->groupBy("c.id", "c.name", "a.delivery_date",  "c.warranty_days", "c.f_sub_category_id", "c.f_category_id")
                    ->get();
            } else {
                $product =  DB::table("finish_products_mst as a")->select(
                    'a.name as product_name',
                    'a.id as product_id',
                    'a.warranty_days as expiry',
                    'a.f_category_id as category_id',
                );
                if ($product_id) {
                    $product->where("a.id", "=", $product_id);
                }
                if ($category_id) {
                    $product->where("a.f_category_id", "=", $category_id);
                }
                if ($sub_category_id) {

                    $product->where("a.f_sub_category_id", "=", $sub_category_id);
                }
                $productNames = $product->get();
            }
        }
     
        return view("barcode", compact("data", "productNames", "f_product_category"));
    }

    public function PrintBarcode(Request $request, $id)
    {
        $data =  DB::table("finish_products_mst")->where("id", $id)->first();
        // if (!$data->manual_barcode) {
        //     return redirect()->back()->with('error', "Barcode not found, Please enter barcode");
        // }
        return view("print-barcode", compact("data"));
    }

    public function PrintallBarcode(Request $request)
    {

        $products = $request->input('products', []);

        $data = collect($products)->map(function ($product) {
            $mst = DB::table("finish_products_mst")
                ->select('price', 'bar_code', 'name',"f_category_id")
                ->where("id", $product['id'])
                ->first();

            return array_merge((array) $mst, [
                'qty'    => $product['qty'],
                'expiry' => $product['expiry'],
    
            ]);
        });
        return view("print-barcode-all", compact("data"));
    }
}
