<?php

namespace App\Http\Controllers;

use App\Models\outlet_customer_order_mst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class posOrderController extends Controller
{
    public function posOrder(Request $request)
    {
        $fromDt = $request->input("fromDt") ?? date("Y-m-d");
        $toDt   = $request->input("toDt") ?? date("Y-m-d");
        $outlet_id = $request->input("outlet_id");

        $outlet = DB::table('outlet')->get();

        $query = outlet_customer_order_mst::with("outletDetails", "customerDetails")
            ->where("invoice_type", "invoice");


        $query->whereDate("created_at", ">=", $fromDt)
            ->whereDate("created_at", "<=", $toDt);


        $query->where("outlet_id", $outlet_id);

        $data = $query->orderBy("id", "desc")
            ->paginate(20);

        return view("pos-order", compact("data", "fromDt", "toDt", "outlet"));
    }


    public function posOrderView(Request $request, $id)
    {
        $data = outlet_customer_order_mst::with("outletDetails", "customerDetails", "productDetails")->where("id", $id)->first();
        return view("pos-order-view", compact("data"));
    }
}
