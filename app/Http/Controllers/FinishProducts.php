<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select; 

class FinishProducts extends Controller
{
    public function CreateProduct(Request $request)
    {
        $location = DB::table("store")->get();
        $finish_product = DB::table("finish_products_mst")->get();
        return view("create-product", compact("location", "finish_product"));
    }

    public function SaveFProducts(Request $request)
    {
        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }
        foreach ($prod_list as $key => $value) {
            $finish_products =   DB::table("finish_products_mst")->where("id", $value->product_id)->first();
            if (empty($finish_products)) {

                return  redirect()->back()->with("error", "Product not found");
            }
            $finish_product_det =   DB::table("finish_products_det as a")
                ->select("a.*", "b.price")
                ->join("products as b", "a.product_id", "b.id")
                ->where("mst_id", $finish_products->id)->get();
            if ($finish_product_det->isEmpty()) {

                return  redirect()->back()->with("error", "Raw material not found");
            }
            try {

                $mst_id = DB::table('product_mst')->insertGetId(array(
                    "location_id" => $value->location_id,
                    "f_product_id" => $finish_products->id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "user_id" => $request->user->id,

                ));
                foreach ($finish_product_det as $k => $v) {
                    DB::table('product_det')->insertGetId(array(
                        "mst_id" => $mst_id,
                        "product_id" => $v->product_id,
                        "qty" => $v->qty,
                        "price" => $v->price,
                        "location_id" => $value->location_id,


                    ));
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function ProductList(Request $request)
    {
        $status = $request->input('status', "pending");
        $products = DB::table("product_mst as a")
            ->select("a.*", "b.name as location", "c.name as product", "d.name as user")
            ->join("store as b", "a.location_id", "b.id")
            ->join("finish_products_mst as c", "a.f_product_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->where("a.status", $status)
            ->orderBy("a.updated_at", "desc")


            ->get();
        return view("product-list", compact("products"));
    }


    public function ProcessProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'qty' => 'required|integer',


        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);
                $count++;
            }
        }



        $product_det = DB::table("product_det")->where("mst_id", $request->id)->get();


        foreach ($product_det as $key => $value) {
            $current_stock = DB::table("current_stock")->where("location_id", $value->location_id)->where("product_id", $value->product_id)->first();
            if ($current_stock->stock >= $value->qty * $request->qty) {


                DB::table("current_stock")->where("id", $current_stock->id)->update([
                    "stock" => $current_stock->stock - ($value->qty * $request->qty)
                ]);
            }
        }
        DB::table("product_mst")
            ->where("id", $request->id)
            ->update([
                "status" => "processing",
                "make_qty" => DB::raw("make_qty + $request->qty")
            ]);
        DB::table("production_products")->insertGetId(array(
            "mst_id" => $request->id,
            "qty" => $request->qty,
            "user_id" => $request->user->id
        ));
        return redirect()->back()->with('success', "Save Successfully");
    }


    public function ProductRawView(Request $request, $id)
    {
        $product_mst = DB::table("product_mst as a")
            ->select("a.*", "b.name as location", "c.name as product")
            ->join("store as b", "a.location_id", "b.id")
            ->join("finish_products_mst as c", "a.f_product_id", "c.id")
            ->where("a.id", $id)->first();
        $product_det = DB::table("product_det as a")
            ->select("a.*", "b.name as product", "b.name", "b.article_no")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.mst_id", $id)
            ->get();

       
        $nextProduct = DB::table("product_mst")
            ->where("id", ">", $id)
            ->orderBy("id", "asc")
            ->first();

        // Get the previous record
        $previousProduct = DB::table("product_mst")
            ->where("id", "<", $id)
            ->orderBy("id", "desc")
            ->first();
        return view("product-raw-material-view", compact("product_mst", "product_det","nextProduct","previousProduct"));
    }
}
