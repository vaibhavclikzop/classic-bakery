<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;


class OutwardStock extends Controller
{
    public function OutwardOrder(Request $request)
    {
        $department = DB::table("department")->get();
        $products = DB::table("products as a")
            ->select("a.*", DB::raw("CASE WHEN b.stock > 0 THEN b.stock ELSE 0 END as stock"))
            ->leftJoin("current_stock as b", "a.id", "b.product_id")
            ->get();
        return view("outward-order", compact("department", "products"));
    }
    public function GetCustomerOrder(Request $request)
    {
        $order_mst =   DB::table("order_mst")->where("customer_id", $request->id)
            ->whereNot("status", "complete")

            ->get();
        return $order_mst;
    }



    public function GetOrderDetails(Request $request)
    {


        $order_det = DB::table("order_det as a")
            ->select("a.*", "b.name", DB::raw("a.qty - a.booked_qty as qty"), "d.stock as stock")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("order_mst as c", "a.mst_id", "=", "c.id")
            ->join("current_stock_products as d", function ($join) {
                $join->on("a.product_id", "=", "d.product_id")
                    ->on("c.location_id", "=", "d.location_id");
            })
            ->where("a.mst_id", $request->id)
            ->whereRaw("a.qty > a.booked_qty")
            ->get();
        return $order_det;
    }

    public function SaveOutward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',

            'invoice_date' => 'required',
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

            $order_mst =  DB::table("order_mst")->where("id", $request->order_id)->first();

            $inv_no =   DB::table("outward_mst")->whereDate("created_at", now())->count();
            if (!$inv_no) {
                $inv_no = 1;
            } else {
                $inv_no++;
            }
            $invoice_prefix =  DB::table("company_settings")->where("id", 1)->first();
            $invoice_id = $invoice_prefix->outward_production_prefix . date('d-m-y') . "-" . $inv_no;


            $mst_id = DB::table('outward_mst')->insertGetId(array(

                "department_id" => $request->department_id,
                "invoice_date" => $request->invoice_date,

                "description" => $request->description,
                "order_id" => $invoice_id,
                "user_id" => $request->user->id,

            ));
            foreach ($prod_list as $k => $v) {
                DB::table('outward_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $v->product_id,
                    "qty" => $v->qty,
                ));


                DB::table('current_stock')->where("product_id", $v->product_id)->decrement("stock", $v->qty);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect('/outward-challan-view/' . $mst_id)
            ->with("success", "Save Successfully");
    }

    public function OutwardOrderList(Request $request)
    {




        $outward =  DB::table("outward_mst as a")
            ->select("a.*", "b.name as department", "c.name as user")
            ->join("department as b", "a.department_id", "b.id")

            ->join("users as c", "a.user_id", "c.id")

            ->orderBy("a.id", "desc")
            ->get();

        return view("outward-order-list", compact("outward"));
    }

    public function DispatchChallan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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

            $mst_id = DB::table('outward_mst')->where("id", $request->id)->update(array(
                "status" => "dispatch",


            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function DeliveredChallan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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

            $mst_id = DB::table('outward_mst')->where("id", $request->id)->update(array(
                "status" => "delivered",


            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function OutwardChallanView(Request $request, $id)
    {
        $order_mst =  DB::table("outward_mst as a")
            ->select("a.*", "b.name as customer_name", "b.contact_person", "b.number")

            ->join("department as b", "a.department_id", "b.id")
            ->where("a.id", $id)
            ->first();
        $order_det = DB::table("outward_det as a")
            ->select("a.*", "b.name as product", "b.article_no", "c.name as sub_category")
            ->join("products as b", "a.product_id", "b.id")
            ->join("sub_category as c", "b.sub_category_id", "c.id")
            ->where("a.mst_id", $id)
            ->get();

        $nextProduct = DB::table("outward_mst")
            ->where("id", ">", $id)
            ->orderBy("id", "asc")
            ->first();

        // Get the previous record
        $previousProduct = DB::table("outward_mst")
            ->where("id", "<", $id)
            ->orderBy("id", "desc")
            ->first();
        return view("outward-challan-view", compact("order_mst", "order_det", "nextProduct", "previousProduct"));
    }

    public function OutwardCustomerOrder()
    {
        $id = request("id");
        $order_mst = DB::table("order_mst")->where("id", $id)->first();
        if ($order_mst->order_type == "customer") {
            $customer =    DB::table("customers")->get();
        } else {
            $customer =    DB::table("outlet")->select("outlet_name as name", "id")->get();
        }

        $f_product_category =    DB::table("f_product_category")->get();
        $mode_of_transport =    DB::table("mode_of_transport")->get();
        return view("outward-customer-order", compact("customer", "mode_of_transport", "f_product_category"));
    }

    public function GetCustomerOrderProduct(Request $request)
    {
        $order_mst =  DB::table("order_det as a")
            ->select("a.*", "b.name as name", "e.name as sub_category",  DB::raw("CASE WHEN c.stock IS NOT NULL THEN c.stock ELSE 0 END as stock"))
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->join("f_product_sub_category as e", "b.f_sub_category_id", "e.id")
            ->leftJoin("finish_product_stock as c", "b.id", "c.product_id")
            ->where("a.mst_id", $request->id)
            ->orderBy("e.name", "asc")
            ->orderBy("b.name", "asc")
            ->get();
        return $order_mst;
    }

    public function SaveCustomerOutward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',

            'invoice_date' => 'required',
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



            $order_no = getInvoiceNo();

            $mst_id = DB::table('outward_customer_order_mst')->insertGetId(array(

                "order_id" => $request->order_id,
                "invoice_date" => $request->invoice_date,

                "description" => $request->description,
                "user_id" => $request->user->id,
                "mode_of_transport" => $request->mode_of_transport,
                "order_no" => $order_no

            ));
            foreach ($prod_list as $k => $v) {

                if ($v->qty > 0) {
                    DB::table('outward_customer_order_det')->insertGetId(array(
                        "mst_id" => $mst_id,
                        "product_id" => $v->product_id,
                        "qty" => $v->qty,
                    ));
                    DB::table('order_det')->where("product_id", $v->product_id)->where("mst_id", $request->order_id)
                        ->increment("booked_qty", $v->qty);
                    DB::table("finish_product_stock")->where("product_id", $v->product_id)->decrement("stock", $v->qty);
                }
            }
            $order_det = DB::table("order_det")->where("mst_id", $request->order_id)->get();
            $status = 0;
            foreach ($order_det as $key => $value) {

                if ($value->booked_qty < $value->qty) {
                    $status = 1;
                }
            }
            if ($status == 0) {
                DB::table("order_mst")->where("id", $request->order_id)->update(array(
                    "status" => "complete",
                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }


        return  redirect("customer-outward-challan-view/$mst_id");
    }


    public function OutwardCustomerOrderList(Request $request)
    {
        $status = request("status", "dispatch");
        $date = request("date", date("Y-m-d"));
        $customer_id = request("customer_id");
        $order_type = request("order_type");


        $dat1 = DB::table("outward_customer_order_mst as a")
            ->select(
                "a.*",
                "c.name as customer",
                "d.name as user",
                "e.name as transport",
                "e.contact_person",
                "e.number",
                "e.vehicle_no",
                "b.delivery_date",
                "b.order_type"
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->leftJoin("mode_of_transport as e", "a.mode_of_transport", "e.id")
            ->where("a.status", $status);


        $dat2 = DB::table("outward_customer_order_mst as a")
            ->select(
                "a.*",
                "c.outlet_name as customer",
                "d.name as user",
                "e.name as transport",
                "e.contact_person",
                "e.number",
                "e.vehicle_no",
                "b.delivery_date",
                "b.order_type"
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("outlet as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->leftJoin("mode_of_transport as e", "a.mode_of_transport", "e.id")
            ->where("a.status", $status);


        if ($date) {
            $dat1->whereDate("a.invoice_date", $date);
            $dat2->whereDate("a.invoice_date", $date);
        }
        if ($customer_id) {
            $dat1->where("b.customer_id", $customer_id);
            $dat2->where("b.customer_id", $customer_id);
        }


        if ($order_type === 'customer') {
            $dat1->where("b.order_type", "customer");
            $data = $dat1->get();
        } elseif ($order_type === 'outlet') {
            $dat2->where("b.order_type", "outlet");
            $data = $dat2->get();
        } else {
            // check both sides
            $hasDat1 = $dat1->where("b.order_type", "customer")->exists();
            $hasDat2 = $dat2->where("b.order_type", "outlet")->exists();

            if ($hasDat1 && $hasDat2) {
                $data = $dat1->union($dat2)->get();
            } elseif ($hasDat1) {
                $data = $dat1->get();
            } elseif ($hasDat2) {
                $data = $dat2->get();
            } else {
                $data = collect();
            }
        }
        $customers = DB::table("customers")->get();

        return view("outward-customer-order-list", compact("data", "customers"));
    }

    public function CustomerOutwardChallanView(Request $request, $id)
    {
        $customer_order =  DB::table("outward_customer_order_mst as a")
            ->select("a.*", "c.name as customer_name",   "c.number")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->where("a.id", $id)
            ->where("b.order_type", "customer")
            ->first();

        $outlet_order =  DB::table("outward_customer_order_mst as a")
            ->select("a.*", "c.outlet_name as customer_name",   "c.number")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("outlet as c", "b.customer_id", "c.id")
            ->where("a.id", $id)
            ->where("b.order_type", "outlet")
            ->first();

        $order_mst = $customer_order ?? $outlet_order;


        $order_det = DB::table("outward_customer_order_det as a")
            ->select(
                "a.qty",
                "b.name as product",
                "b.article_no",
                "c.name as sub_category",
                "d.price"
            )
            ->join("outward_customer_order_mst as e", "a.mst_id", "=", "e.id") // Join 'e' first
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("f_product_sub_category as c", "b.f_sub_category_id", "=", "c.id")
            ->join("order_det as d", function ($join) {
                $join->on("a.product_id", "=", "d.product_id")
                    ->on("e.order_id", "=", "d.mst_id");
            })
            ->where("a.mst_id", $id)
            ->groupBy("a.qty", "b.name", "b.article_no", "c.name", "d.price", "a.product_id")
            ->get();


        $nextProduct = DB::table("outward_customer_order_mst")
            ->where("id", ">", $id)

            ->orderBy("id", "asc")
            ->first();

        // Get the previous record
        $previousProduct = DB::table("outward_customer_order_mst")
            ->where("id", "<", $id)
            ->orderBy("id", "desc")
            ->first();
        return view("customer-outward-challan-view", compact("order_mst", "order_det", "nextProduct", "previousProduct"));
    }

    public function SaveCustomerOutwardStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',


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

        DB::table("outward_customer_order_mst")->where("id", $request->id)->update(array(
            "status" => "delivered"
        ));
        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function Invoices(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();
        $order_type = request("order_type");

        $status = request("status", "dispatch");
        $query = DB::table("outward_customer_order_mst as a")
            ->select(
                "a.*",
                DB::raw("CASE 
            WHEN b.order_type = 'customer' THEN c.name 
            ELSE o.outlet_name 
        END as customer"),
                "d.name as user",
                "e.name as transport",
                "e.contact_person",
                "e.number",
                "e.vehicle_no"
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->leftJoin("customers as c", "b.customer_id", "c.id")
            ->leftJoin("outlet as o", "b.customer_id", "o.id")
            ->join("users as d", "a.user_id", "d.id")
            ->leftJoin("mode_of_transport as e", "a.mode_of_transport", "e.id")
            ->where("a.is_invoice", 1);

        if ($order_type) {
            $query->where("b.order_type", $order_type);
        }

        if ($fromDt) {

            $query->whereDate("a.invoice_date", ">=", $fromDt);
        }

        if ($toDt) {
            $query->whereDate("a.invoice_date", "<=", $toDt);
        }

        $data = $query->orderBy("a.id", "desc")->get();

        $customers = DB::table("customers")->get();
        return view("invoices", compact("data"));
    }

    public function InvoiceView(Request $request, $id)
    {
        $customer =  DB::table("outward_customer_order_mst as a")
            ->select("a.*", "c.name as customer_name", "c.name as customer", "c.number", "c.*", "d.name as mot", "d.vehicle_no",  "a.order_no")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("mode_of_transport as d", "a.mode_of_transport", "d.id")
            ->where("a.id", $id)
            ->where("b.order_type", "customer")
            ->first();

        $outlet = DB::table("outward_customer_order_mst as a")
            ->select(
                "a.*",
                "c.outlet_name as customer_name",
                "c.number",
                "e.*",
                "d.name as mot",
                "d.vehicle_no",
                "e.gst_no as gst",
                "e.fssai_no as ship_fssai_no",
                "e.gst_no as ship_gst",
                "e.address as ship_address",
                "a.order_no",
                DB::raw("'pincode' as pincode,'city' as city,'state' as state,'ship_city','ship_state','ship_pincode'")
            )
            ->join("order_mst as b", "a.order_id", "=", "b.id")
            ->join("outlet as c", "b.customer_id", "=", "c.id")
            ->join("mode_of_transport as d", "a.mode_of_transport", "=", "d.id")
            ->join("company_settings as e", "c.id", "=", "e.outlet_id")
            ->where("a.id", $id)
            ->where("b.order_type", "outlet")
            ->first();


        $order_mst = $customer ?? $outlet;


        $order_det = DB::table("outward_customer_order_det as a")
            ->select(
                "a.*",
                "b.name as product",
                "b.article_no",
                "c.name as sub_category",
                "d.price",
                "b.*",
                "e.name as uom",
                "d.mrp",
                "d.cess_amt",
                "d.gst_type",
                "d.gst as gst",
                "d.price"
            )
            ->join("outward_customer_order_mst as f", "a.mst_id", "=", "f.id")
            ->join("order_det as d", function ($join) {
                $join->on("a.product_id", "=", "d.product_id")
                    ->whereColumn("d.mst_id", "=", "f.order_id");
            })
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("f_product_sub_category as c", "b.f_sub_category_id", "=", "c.id")
            ->join("unit_type as e", "b.uom", "=", "e.id")
            ->where("a.mst_id", $id)
            ->orderByRaw("LOWER(b.name) ASC")

            ->get();


        $nextProduct = DB::table("outward_customer_order_mst")
            ->where("id", ">", $id)
            ->orderBy("id", "asc")
            ->first();

        // Get the previous record
        $previousProduct = DB::table("outward_customer_order_mst")
            ->where("id", "<", $id)
            ->orderBy("id", "desc")
            ->first();
        return view("invoice-view", compact("order_mst", "order_det", "nextProduct", "previousProduct"));
    }
}
