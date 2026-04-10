<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseReturn extends Controller
{
    public function PurchaseReturnList(Request $request)
    {
        $vendors = DB::table("vendor as a")
            ->get();

        $data =   DB::table("purchase_return_mst as a")
            ->select("a.*", "b.name as vendor", "c.name as company", "d.name as user", "e.invoice_no")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->leftJoin("company as c", "b.company_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_inward_mst as e", "a.inward_id", "e.id")
            ->get();
        return view("purchase-return", compact("vendors", "data"));
    }

    public function GetInwardChallan(Request $request)
    {
        $data = DB::table("stock_inward_mst")->where("vendor_id", $request->id)->get();
        return $data;
    }

    public function GetInwardChallanProducts(Request $request)
    {

        $rm = DB::table("stock_inward_det as a")
            ->select(
                "a.id",
                "a.product_id",
                "a.type",
                DB::raw("CAST(b.name AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) as product"),
                DB::raw("a.qty - a.return_qty as qty")
            )
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)
            ->where("a.type", "raw material");

        $fg = DB::table("stock_inward_det as a")
            ->select(
                "a.id",
                "a.product_id",
                "a.type",
                DB::raw("CAST(b.name AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) as product"),
                DB::raw("a.qty - a.return_qty as qty")
            )
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)
            ->where("a.type", "finished product");

        $data = $rm->union($fg)->get();


        return $data;
    }

    public function SavePurchaseReturn(Request $request)
    {
        $po_id = 'PO_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'inward_id' => 'required',


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
            $mst_id = DB::table('purchase_return_mst')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "user_id" => $request->user->id,
                "inward_id" => $request->inward_id,
                "return_date" => $request->return_date,
                "description" => $request->description,


            ));



            foreach ($prod_list as $key => $value) {
                DB::table('purchase_return_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "type" => $value->type,

                ));

                DB::table('stock_inward_det')->where("mst_id", $request->inward_id)->where("product_id", $value->product_id)->increment("return_qty", $value->qty);
                DB::table('current_stock')->where("product_id", $value->product_id)->decrement("stock", $value->qty);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function PurchaseReturnChallanView(Request $request, $id)
    {
        $po_mst =   DB::table("purchase_return_mst as a")
            ->select("a.*","a.id as debit_note_no", "b.name as vendor", "c.name as company", "d.name as user", "e.invoice_no", "b.address", "b.state", "b.city", "b.pincode", "b.number", "b.email", "b.gst")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->leftJoin("company as c", "b.company_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_inward_mst as e", "a.inward_id", "e.id")
            ->where("a.id", $id)
            ->first();
        $stock_inward_mst = DB::table("stock_inward_mst")
            ->where("id", $po_mst->inward_id)
            ->first();

        // $rm = DB::table("purchase_return_det as a")
        //     ->select(
        //         "a.id","a.qty",
        //         DB::raw("CAST(b.name AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) as product")
        //     )
        //     ->join("products as b", "a.product_id", "b.id")
        //     ->where("a.type", "raw material")
        //     ->where("a.mst_id", $id);

        // $fg = DB::table("purchase_return_det as a")
        //     ->select(
        //         "a.id","a.qty",
        //         DB::raw("CAST(b.name AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) as product")
        //     )
        //     ->join("finish_products_mst as b", "a.product_id", "b.id")
        //     ->where("a.type", "finished product")
        //     ->where("a.mst_id", $id);

        // $po_det = $rm->union($fg)->get();
        $rm = DB::table("purchase_return_det as a")
            ->select(
                "a.id",
                "a.qty",
                DB::raw("b.name COLLATE utf8mb4_unicode_ci as product"),
                "b.hsn_code",
                "u.name as uom",
                "sid.price",
                "sid.gst",
                "sid.cess_tax",
                DB::raw("(a.qty * sid.price) as sub_total"),
                DB::raw("((a.qty * sid.price) * sid.gst / 100) as gst_amount"),
                DB::raw("((a.qty * sid.price) * sid.cess_tax / 100) as cess_amount"),
                DB::raw("(a.qty * sid.price) 
            + ((a.qty * sid.price) * sid.gst / 100) 
            + ((a.qty * sid.price) * sid.cess_tax / 100) as total")
            )
            ->join("products as b", "a.product_id", "b.id")
            ->leftJoin("unit_type as u", "b.uom", "u.id")
            ->join("purchase_return_mst as prm", "a.mst_id", "prm.id")
            ->join("stock_inward_det as sid", function ($join) {
                $join->on("sid.product_id", "=", "a.product_id")
                    ->on("sid.type", "=", "a.type");
            })
            ->whereColumn("sid.mst_id", "prm.inward_id")
            ->where("a.type", "raw material")
            ->where("a.mst_id", $id);


        $fg = DB::table("purchase_return_det as a")
            ->select(
                "a.id",
                "a.qty",
                DB::raw("b.name COLLATE utf8mb4_unicode_ci as product"),
                "b.hsn_code",
                "u.name as uom",
                "sid.price",
                "sid.gst",
                "sid.cess_tax",

                DB::raw("(a.qty * sid.price) as sub_total"),
                DB::raw("((a.qty * sid.price) * sid.gst / 100) as gst_amount"),
                DB::raw("((a.qty * sid.price) * sid.cess_tax / 100) as cess_amount"),
                DB::raw("(a.qty * sid.price) 
            + ((a.qty * sid.price) * sid.gst / 100) 
            + ((a.qty * sid.price) * sid.cess_tax / 100) as total")
            )
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->leftJoin("unit_type as u", "b.uom", "u.id")
            ->join("purchase_return_mst as prm", "a.mst_id", "prm.id")
            ->join("stock_inward_det as sid", function ($join) {
                $join->on("sid.product_id", "=", "a.product_id")
                    ->on("sid.type", "=", "a.type");
            })
            ->whereColumn("sid.mst_id", "prm.inward_id")
            ->where("a.type", "finished product")
            ->where("a.mst_id", $id);


        $po_det = $rm->union($fg)->get();

        return view("purchase-return-challan-view", compact("po_mst", "po_det", "stock_inward_mst"));
    }
}
