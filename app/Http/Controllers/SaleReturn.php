<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleReturn extends Controller
{
    public function SaleReturnList(Request $request)
    {

        $status = request("status");
        $customers =  DB::table("customers")->get();
        $order_type =  DB::table("order_type")->get();

        $order = DB::table("sale_return_mst as a")
            ->select("a.*", "b.name as customer", "e.name as user")
            ->join("customers as b", "a.customer_id", "=", "b.id")
            ->join("users as e", "a.user_id", "=", "e.id")
            ->where("a.order_type", "customer");

        if ($status) {
            $order->where("a.status", $status);
        }


        $outlet = DB::table("sale_return_mst as a")
            ->select("a.*", "b.outlet_name as customer", "e.name as user")
            ->join("outlet as b", "a.customer_id", "=", "b.id")
            ->join("users as e", "a.user_id", "=", "e.id")
            ->where("a.order_type", "outlet");
        if ($status) {
            $outlet->where("a.status", $status);
        }


        $data = $order->union($outlet)
            ->orderBy("id", "desc")
            ->get();
        return view("sale-return", compact("customers", "data", "order_type"));
    }

    public function GetOutwardChallan(Request $request)
    {
        $data = DB::table("order_mst as a")
            ->select("a.*", "b.id as id")
            ->join("outward_customer_order_mst as b", "a.id", "b.order_id")
            ->where("a.customer_id", $request->id)->get();
        return $data;
    }

    public function GetOutwardChallanProducts(Request $request)
    {

        $data = DB::table("outward_customer_order_det as a")
            ->select("a.*", "b.name as product", DB::raw("a.qty-a.return_qty as qty"))
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)->get();
        return $data;
    }

    public function SaveSaleReturn(Request $request)
    {
        $po_id = 'PO_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',


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
        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }

        try {
            $mst_id = DB::table('sale_return_mst')->insertGetId(array(
                "customer_id" => $request->customer_id,
                "user_id" => $request->user->id,

                "return_date" => $request->return_date,
                "description" => $request->description,
                "status" => $request->status,
                "order_type" => "customer",


            ));
            foreach ($prod_list as $key => $value) {
                DB::table('sale_return_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "type" => $value->type,

                ));

                // DB::table('outward_customer_order_det')->where("mst_id", $request->outward_id)->where("product_id", $value->product_id)->increment("return_qty", $value->qty);
                // if($value->type=="scrap"){
                //   $finish_goods_defective_stock=  DB::table("finish_goods_defective_stock")->where("product_id",$value->product_id)->first();
                //   if($finish_goods_defective_stock){
                //     DB::table("finish_goods_defective_stock")->where("product_id",$value->product_id)->increment("qty",$value->qty);
                //   }else{
                //     DB::table("finish_goods_defective_stock")->insertGetId(array(
                //         "product_id"=>$value->product_id,
                //         "qty"=>$value->qty,
                //     ));
                //   }
                // }else{
                //     DB::table("finish_product_stock")->where("product_id",$value->product_id)->increment("stock",$value->qty);
                // }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }




    public function SaleReturnChallanView(Request $request, $id)
    {


        $customer =   DB::table("sale_return_mst as a")
            ->select("a.*", "b.name as customer", "d.name as user", "b.address", "b.state", "b.city",   "b.number", "b.email", "b.gst")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as d", "a.user_id", "d.id")
            ->where("a.id", $id)
            ->where("a.order_type", "customer")
            ->first();


        $outlet =   DB::table("sale_return_mst as a")
            ->select("a.*", "b.outlet_name as customer", "d.name as user", "b.address", "b.state", "b.city",   "b.number", "e.email", "e.gst_no as gst")
            ->join("outlet as b", "a.customer_id", "b.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company_settings as e", "b.id", "e.outlet_id")
            ->where("a.id", $id)
            ->where("a.order_type", "outlet")
            ->first();

        $po_mst = $customer ?? $outlet;
        $po_det = DB::table("sale_return_det as a")
            ->select("a.*", "b.name as product")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.mst_id", $id)
            ->get();

        return view("sale-return-challan-view", compact("po_mst", "po_det"));
    }

    public function SaleReturnApprove(Request $request, $id)
    {
        $customer =   DB::table("sale_return_mst as a")
            ->select("a.*", "b.name as customer", "d.name as user", "b.address", "b.state", "b.city",   "b.number", "b.email", "b.gst")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as d", "a.user_id", "d.id")
            ->where("a.id", $id)
            ->where("a.order_type", "customer")
            ->first();


        $outlet =   DB::table("sale_return_mst as a")
            ->select("a.*", "b.outlet_name as customer", "d.name as user", "b.address", "b.state", "b.city",   "b.number", "e.email", "e.gst_no as gst")
            ->join("outlet as b", "a.customer_id", "b.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company_settings as e", "b.id", "e.outlet_id")
            ->where("a.id", $id)
            ->where("a.order_type", "outlet")
            ->first();

        $po_mst = $customer ?? $outlet;
        $po_det = DB::table("sale_return_det as a")
            ->select("a.*", "b.name as product", "b.price")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.mst_id", $id)
            ->get();

        return view("sale-return-approve", compact("po_mst", "po_det"));
    }

    public function SaveSaleReturnApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mst_id' => 'required',


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

        try {
            //code...

            foreach ($request->type as $key => $value) {
                $sale_return_det =  DB::table("sale_return_det")->where("id", $key)->first();
                if ($sale_return_det) {


                    $price = $request->price[$key][0] ?? 0;
                    DB::table("sale_return_det")->where("id", $key)->update(array(
                        "type" => $value[0],
                        "mrp" => $price
                    ));
                    if ($value[0] == "current_stock") {


                        $cs =  DB::table("finish_product_stock")->where("product_id", $sale_return_det->product_id)->first();
                        if ($cs) {
                            DB::table("finish_product_stock")->where("product_id", $sale_return_det->product_id)->increment("stock", $sale_return_det->qty);
                        } else {
                            DB::table("finish_product_stock")->insertGetId(array(
                                "product_id" => $sale_return_det->product_id,
                                "stock" => $sale_return_det->qty,
                            ));
                        }
                    }
                }
            }
            DB::table("sale_return_mst")->where("id", $request->mst_id)->update(array(
                "status" => "complete"
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect("/sale-return")->with('success', "Save Successfully");
    }
}
