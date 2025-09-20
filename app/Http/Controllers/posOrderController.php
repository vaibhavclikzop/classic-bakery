<?php

namespace App\Http\Controllers;

use App\Models\outlet_customer_order_mst;
use Illuminate\Http\Request;

class posOrderController extends Controller
{
    public function posOrder(Request $request)
    {
        $data = outlet_customer_order_mst::with("outletDetails", "customerDetails")->orderby("id", "desc")->where("invoice_type","invoice")->get();
        return view("pos-order", compact("data"));
    }

    public function posOrderView(Request $request,$id)
    {
        $data = outlet_customer_order_mst::with("outletDetails", "customerDetails","productDetails")->where("id",$id)->first();
        return view("pos-order-view",compact("data"));
    }
}
