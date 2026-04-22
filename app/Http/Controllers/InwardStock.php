<?php

namespace App\Http\Controllers;

use App\Models\stock_inward_det_finish_goods;
use App\Models\stock_inward_mst_finish_goods;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class InwardStock extends Controller
{

    public function GetVendorProducts(Request $request)
    {
        $type = $request->type;
        if ($type == "raw material") {
            $products = DB::table("products as a")
                ->select("a.*")
                ->join("vendor_product as b", "a.id", "b.product_id")
                ->where("b.vendor_id", $request->id)
                ->where("b.active", 1)
                ->where("a.active", 1)
                ->orderBy("a.name", "asc")
                ->get();
        } else {
            $products = DB::table("finish_products_mst as a")
                ->select("a.*")
                ->join("vendor_product_finish_goods as b", "a.id", "b.product_id")
                ->where("b.vendor_id", $request->id)
                ->where("a.active", 1)
                ->orderBy("a.name", "asc")
                ->get();
        }


        return $products;
    }

    public function GeneratePO(Request $request)
    {
        $vendor = DB::table("vendor")->orderBy("name", "asc")->get();
        return view("generate-po", compact("vendor"));
    }
    public function SavePO(Request $request)
    {

        $po_id = 'PO_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'name' => 'required',

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
        $gst_type = "";
        $vendor = DB::table("vendor")->where("id", $request->vendor_id)->first();
        $company_setting = DB::table("company_settings")->where("id", 1)->first();
        if ($vendor->city && $company_setting->city) {
            if ($vendor->city == $company_setting->city) {
                $gst_type = "Inner GST";
            } else {
                $gst_type = "Outer GST";
            }
        } else {
            return redirect()->back()->with('error', "Select vendor city");
        }


        $status = "pending";
        if ($request->user->id == 1) {
            $status = "generated";
        }


        $inv_no =   DB::table("po_mst")->whereDate("created_at", now())->count();
        if (!$inv_no) {
            $inv_no = 1;
        } else {
            $inv_no++;
        }
        $invoice_prefix =  DB::table("company_settings")->where("id", 1)->first();



        $today = now();


        if ($today->month >= 4) {
            $startYear = $today->year;
            $endYear = $today->year + 1;
        } else {
            $startYear = $today->year - 1;
            $endYear = $today->year;
        }

        $financialYear = $startYear . "-" . substr($endYear, -2);

        $regularExistsInFY = DB::table("po_mst")
            ->where("financial_year", "=", $financialYear)
            ->exists();

        if ($regularExistsInFY) {
            $maxRegular = DB::table("po_mst")
                ->where("financial_year", "=", $financialYear)
                ->count("id");
            $nextNumber = $maxRegular + 1;
        } else {
            $nextNumber = 1;
        }

        $invoice_id = $invoice_prefix->po_prefix . date('d-m-y') . "-" . $nextNumber;

        $exists = DB::table('po_mst')
            ->where('po_id', $invoice_id)
            ->exists();

        if ($exists) {


            do {

                $invoice_id = $invoice_prefix->po_prefix . date('d-m-y') . "-" . $nextNumber;

                $exists = DB::table('po_mst')
                    ->where('po_id', $invoice_id)
                    ->exists();

                $nextNumber++;
            } while ($exists);
        }



        DB::beginTransaction();
        try {
            $mst_id = DB::table('po_mst')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "user_id" => $request->user->id,
                "po_id" => $invoice_id,
                "name" => $request->name,
                "description" => $request->description,
                "status" => $status,
                "created_at" => now(),
                "financial_year" => $financialYear

            ));



            foreach ($prod_list as $key => $value) {
                if ($value->type == "raw material") {
                    $products =   DB::table("products")->where("id", $value->product_id)->first();
                } else {
                    $products =   DB::table("finish_products_mst")->where("id", $value->product_id)->first();
                }


                DB::table('po_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "gst" => $value->gst,
                    "gst_type" => $gst_type,
                    "cess_tax" => $products->cess_tax,
                    "type" => $value->type

                ));
            }
            DB::commit();


            return redirect("purchase-order-view/" . $mst_id)->with('success', "Save Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function PurchaseOrder(Request $request, $status)
    {

        $po = $request->input('po', 1);
        $where = [];

        if ($po == 2) {
            $where[] = ['a.order_id', '=', 0];
        }
        if ($po == 3) {
            $where[] = ['a.order_id', '>', 0];
        }

        $fromDt = request("fromDt");
        $toDt = request("toDt");

        $po = DB::table("po_mst as a")
            ->select("a.*", "b.name as vendor_name", "c.name as user_name")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")

            ->where("a.status", $status)
            ->where($where);
        if ($fromDt) {
            $po->whereDate("a.created_at", ">=", $fromDt);
        }
        if ($toDt) {
            $po->whereDate("a.created_at", "<=", $toDt);
        }

        $po_mst =  $po->orderBy("a.id", "desc")
            ->get();
        return view("purchase-order", compact("po_mst", "status"));
    }
    public function PurchaseOrderView(Request $request, $id)
    {
        $po_mst = DB::table("po_mst as a")
            ->select("a.*", "b.name as vendor_name", "b.number as vendor_number", "b.email as vendor_email", "b.address as vendor_address", "b.state as vendor_state", "b.city as vendor_city", "b.pincode as vendor_pincode", "b.gst as vendor_gst", "b.company_name")
            ->join("vendor as b", "a.vendor_id", "b.id")

            ->where("a.id", $id)->first();




        $poRM = DB::table("po_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci as product_name, c.name COLLATE utf8mb4_unicode_ci as sub_category")
            ->join("products as b", "a.product_id", "b.id")
            ->join("sub_category as c", "b.sub_category_id", "c.id")
            ->where("a.mst_id", $po_mst->id)
            ->where("a.type", "raw material");

        $poFG = DB::table("po_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci as product_name, c.name COLLATE utf8mb4_unicode_ci as sub_category")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->join("f_product_sub_category as c", "b.f_sub_category_id", "c.id")
            ->where("a.mst_id", $po_mst->id)
            ->where("a.type", "finished product");

        $po_det = $poRM->union($poFG)->get();

        $rm = DB::table("products as a")
            ->select("a.*")
            ->join("vendor_product as b", "a.id", "b.product_id")
            ->where("b.vendor_id", $po_mst->vendor_id)
            ->where("a.active", 1)->get();
        $fg = DB::table("finish_products_mst as a")
            ->select("a.*")
            ->join("vendor_product_finish_goods as b", "a.id", "b.product_id")
            ->where("b.vendor_id", $po_mst->vendor_id)
            ->where("a.active", 1)->get();


        return view("purchase-order-view", compact("po_mst", "po_det", "rm", "fg"));
    }




    public function InwardStock(Request $request)
    {
        $vendor = DB::table("vendor")

            ->orderBy("name", "asc")
            ->get();
        $store = DB::table("store")->get();
        return view("inward-stock", compact("vendor", "store"));
    }

    public function GetPO(Request $request)
    {
        $po_mst = DB::table('po_mst')
            ->where('vendor_id', $request->id)
            ->where(function ($query) {
                $query->where('status', 'partial')
                    ->orWhere('status', 'generated');
            })
            ->get();
        return $po_mst;
    }

    public function GetPODet(Request $request)
    {

        $poRM = DB::table("po_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci  as product_name, b.article_no, b.id as product_id")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.type", "raw material")
            ->whereIn("mst_id", $request->id);

        $poFG = DB::table("po_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci as product_name, b.article_no, b.id as product_id")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.type", "finished product")
            ->whereIn("mst_id", $request->id);
        $po_det = $poRM->union($poFG)->get();
        return $po_det;
    }


    public function SaveInwardStock(Request $request)
    {



        $inward_id = 'Inward_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'po_id' => 'required',


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


        $poGrouped = collect($prod_list)->groupBy('po_id');

        try {
            DB::beginTransaction();



            $invoice_prefix =  DB::table("company_settings")->where("id", 1)->first();



            $today = now();


            if ($today->month >= 4) {
                $startYear = $today->year;
                $endYear = $today->year + 1;
            } else {
                $startYear = $today->year - 1;
                $endYear = $today->year;
            }

            $financialYear = $startYear . "-" . substr($endYear, -2);

            $regularExistsInFY = DB::table("stock_inward_mst")
                ->where("financial_year", "=", $financialYear)
                ->exists();

            if ($regularExistsInFY) {
                $maxRegular = DB::table("stock_inward_mst")
                    ->where("financial_year", "=", $financialYear)
                    ->count("id");
                $nextNumber = $maxRegular + 1;
            } else {
                $nextNumber = 1;
            }

            $invoice_id = $invoice_prefix->invoice_prefix . date('d-m-y') . "-" . $nextNumber;

            $exists = DB::table('stock_inward_mst')
                ->where('invoice_id', $invoice_id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Invoice already exists');
            }

            $mst_id = DB::table('stock_inward_mst')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "po_id" => implode(", ", $request->po_id),
                "invoice_no" => $request->invoice_no,
                "invoice_id" => $invoice_id,
                "invoice_date" => $request->invoice_date,
                "received_material_date" => $request->received_material_date,
                "description" => $request->description,
                "user_id" => $request->user->id,
                "delivery_charges" =>  $request->delivery_charges,
                "financial_year" => $financialYear,

            ));

            foreach ($poGrouped as $po_id => $products) {

                $status = 0;
                foreach ($products as $value) {


                    $check_po_det = DB::table('po_det')->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->first();
                    if ($check_po_det) {



                        $det_id = DB::table('stock_inward_det')->insertGetId(array(
                            "mst_id" => $mst_id,
                            "product_id" => $value->product_id,
                            "qty" => $value->qty,
                            "price" => $value->price,
                            "gst" => $value->gst,
                            "cess_tax" => $check_po_det->cess_tax,
                            "sno" => 0,
                            "type" => $check_po_det->type,
                            "po_id" => $po_id,
                        ));


                        DB::table('po_det')->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->increment("received_qty", $value->qty);


                        if ($check_po_det->type == "raw material") {
                            $current_stock = DB::table("current_stock")->where("product_id", $value->product_id)->first();

                            if ($current_stock) {
                                DB::table('current_stock')->where("id", $current_stock->id)->update([
                                    'stock' => DB::raw('stock + ' . $value->qty)
                                ]);
                            } else {
                                DB::table('current_stock')->insertGetId(array(

                                    "product_id" => $value->product_id,
                                    "stock" => $value->qty,
                                ));
                            }
                        } else {
                            $current_stock = DB::table("finish_product_stock")->where("product_id", $value->product_id)->first();

                            if ($current_stock) {
                                DB::table('finish_product_stock')->where("id", $current_stock->id)->update([
                                    'stock' => DB::raw('stock + ' . $value->qty)
                                ]);
                            } else {
                                DB::table('finish_product_stock')->insertGetId(array(

                                    "product_id" => $value->product_id,
                                    "stock" => $value->qty,
                                ));
                            }
                        }



                        $po_det = DB::table('po_det')->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->first();

                        if ($po_det->received_qty < $po_det->qty) {
                            $status = 1;
                        }
                        if ($status == 1) {
                            DB::table('po_mst')->where("id", $value->po_id)->update(array(
                                "status" => "partial",
                            ));
                        } else {
                            DB::table('po_mst')->where("id", $value->po_id)->update(array(
                                "status" => "complete",
                            ));
                        }
                    }
                }
            }




            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect("/inward-report-view/$mst_id")->with('success', "Save Successfully");
    }



    public function GetRawMaterial(Request $request)
    {
        $finish_products_det = DB::table("finish_products_det as a")
            ->select("a.*", "b.name as product_name", "b.article_no as article_no", "b.price")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)->get();
        return $finish_products_det;
    }







    public function CheckCurrentStock(Request $request)
    {
        $product_mst = DB::table("product_mst")->where("id", $request->id)->first();
        if (!$product_mst) {
            return json_encode(['error' => true, "msg" => "Product Id not found"]);
        }
        $error = false;
        $msg = "";
        $product_det = DB::table("product_det as a")
            ->select(
                "a.*",
                "p.name as product_name",
                DB::raw("COALESCE(b.stock, 0) as stock")
            )
            ->leftJoin("current_stock as b", function ($join) {
                $join->on("a.product_id", "=", "b.product_id")
                    ->on("a.location_id", "=", "b.location_id");
            })
            ->join("products as p", "a.product_id", "=", "p.id")
            ->where("a.mst_id", $request->id)
            ->get();
        foreach ($product_det as $key => $value) {
            if ($value->qty * $request->qty > $value->stock) {
                $value->status = true;
                $error = true;
                $value->qty = $value->qty * $request->qty;
                $msg = "Qty more then current stock.";
            } else {
                $value->status = false;
                $value->qty = $value->qty * $request->qty;
            }
        }
        return json_encode(["error" => $error, "data" => $product_det, "msg" => $msg]);
    }



    public function CompleteGenSet(Request $request)
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

        $gen_set_mst = DB::table("gen_set_mst")->where("id", $request->id)->first();
        if ($gen_set_mst) {

            $current_stock_genset = DB::table("current_stock_genset")->where("product_id", $gen_set_mst->f_product_id)->where("location_id", $gen_set_mst->location_id)->first();


            if ($current_stock_genset) {
                DB::table("current_stock_genset")->where("id", $current_stock_genset->id)->increment("stock");
            } else {
                $mst_id = DB::table('current_stock_genset')->insertGetId(array(
                    "product_id" => $gen_set_mst->f_product_id,

                    "location_id" => $gen_set_mst->location_id,
                    "stock" => 1,

                ));
            }
            DB::table("gen_set_mst")->where("id", $request->id)->update(array(
                "status" => "complete"
            ));
        } else {
            return redirect()->back()->with('error', "GenSet ID not found");
        }
        return redirect()->back()->with('success', "Save successfully");
    }
    public function ViewGenSetDetails(Request $request, $id)
    {

        $gen_set_mst = DB::table("gen_set_mst as a")
            ->select("a.*", "t.name as team", "fp.name as product", "s.name as location", "u.name as user")
            ->join("team as t", "a.team_id", "t.id")
            ->join("finish_products_mst as fp", "a.f_product_id", "fp.id")
            ->join("store as s", "a.location_id", "s.id")
            ->join("users as u", "a.user_id", "u.id")
            ->where("a.id", $id)
            ->first();
        $gen_set_det = DB::table("gen_set_det as a")
            ->select("a.*", "b.name as product", "b.article_no")
            ->join("products as b", "a.product_id", "b.id")
            ->where("mst_id", $id)->get();

        $products = DB::table("products")->get();

        return view("view-gen-set-details", compact("gen_set_mst", "gen_set_det", "products"));
    }

    public function SaveGeneratePO(Request $request)
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
            DB::table('po_mst')->where("id", $request->id)->update(array(

                "status" => "generated",
                // "name" => $request->name,
                // "description" => $request->description,
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }


    public function SaveFGGeneratePO(Request $request)
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
            DB::table('po_mst_finish_goods')->where("id", $request->id)->update(array(

                "status" => "generated",
                "name" => $request->name,
                "description" => $request->description,
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function GetSerialNumber(Request $request)
    {
        $stock_inward_det = DB::table("inward_det_qty as a")
            ->select("a.*", "b.name as product_name")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.out_status", 0)
            ->where("a.product_id", $request->product_id)
            ->where("a.location_id", $request->location_id)->get();
        return $stock_inward_det;
    }


    public function DeleteGenSet(Request $request)
    {
        $gen_set_mst =  DB::table("gen_set_mst")->where("id", $request->id)->first();
        if ($gen_set_mst->is_order == 1 && $gen_set_mst->order_id > 0) {
            DB::table("order_mst")->where("id", $gen_set_mst->order_id)->delete();
            DB::table("order_det")->where("mst_id", $gen_set_mst->order_id)->delete();
        }

        DB::table("gen_set_mst")->where("id", $gen_set_mst->id)->delete();
        DB::table("gen_set_det")->where("mst_id", $gen_set_mst->id)->delete();
        return redirect()->back()->with("success", "Save successfully");
    }
    public function InwardFinishGoods(Request $request)
    {

        $department_id = request("department", "all");

        $date = request("date", date('Y-m-d'));

        $finish_inward_mst =   DB::table("finish_inward_mst")->whereDate("date", $date)->first();
        $finish_inward_det = collect();
        if ($finish_inward_mst) {
            $finish_inward =  DB::table("finish_inward_det as a")
                ->select("a.*", "b.name as product", "c.name as sub_category")
                ->join("finish_products_mst as b", "a.product_id", "b.id")
                ->join("f_product_sub_category as c", "b.f_sub_category_id", "c.id")
                ->where("a.mst_id", $finish_inward_mst->id);

            if ($finish_inward_mst->status == 0) {
                $finish_inward->whereColumn("a.qty", ">", "a.inward_qty");
            }

            if ($department_id != "all") {
                $finish_inward->where("b.department_id", $department_id);
            }

            $finish_inward_det = $finish_inward->get();
        }
        $department = DB::table("department")->get();
        return view("inward-finish-goods", compact("finish_inward_det", "finish_inward_mst", "department"));
    }


    public function SaveInwardFinishGoods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'inward_qty' => 'required',


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
        DB::beginTransaction();
        try {

            $department_id = request("department_id", "all");

            $where =  DB::table("finish_inward_mst as a")

                ->join("finish_inward_det as b", "a.id", "b.mst_id")
                ->join("finish_products_mst as c", "b.product_id", "c.id")
                ->where("date", $request->date);
            if ($department_id != "all") {

                $where->where("c.department_id", $department_id);
            }
            $where->update(array(
                "status" => 1
            ));
            DB::table("order_mst")
                ->where("delivery_date", $request->date)
                ->where("status", "processing")
                ->update(array(
                    "status" => "dispatch"
                ));
            foreach ($request->inward_qty as $key => $value) {
                $finish_inward_det =  DB::table("finish_inward_det")->where("id", $key)->first();
                DB::table("finish_inward_det")->where("id", $finish_inward_det->id)->increment("inward_qty", $value[0]);

                $finish_product_stock = DB::table("finish_product_stock")->where("product_id", $finish_inward_det->product_id)->first();
                if ($finish_product_stock) {
                    DB::table("finish_product_stock")->where("product_id", $finish_inward_det->product_id)->increment("stock", $value[0]);
                } else {
                    DB::table("finish_product_stock")->insertGetId(array(
                        "product_id" => $finish_inward_det->product_id,
                        "stock" => $value[0],
                    ));
                }
            }

            $completeMstIds = DB::table('finish_inward_det as a')
                ->join('finish_inward_mst as b', 'a.mst_id', '=', 'b.id')
                ->whereDate('b.date', $request->date)
                ->groupBy('a.mst_id')
                ->havingRaw('SUM(CASE WHEN a.qty > a.inward_qty THEN 1 ELSE 0 END) = 0')
                ->pluck('a.mst_id');

            DB::table('finish_inward_mst')
                ->whereIn('id', $completeMstIds)
                ->update(['status' => 1]);

            DB::table('finish_inward_mst')
                ->whereDate('date', $request->date)
                ->whereNotIn('id', $completeMstIds)
                ->update(['status' => 0]);


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', "Save Successfully");
    }

    public function DirectInward(Request $request)
    {
        $finish_products_mst =  DB::table("finish_products_mst")->get();
        return view("direct-inward", compact("finish_products_mst"));
    }

    public function SaveDirectInward(Request $request)
    {
        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }

        $mst_id =  Db::table("direct_inward_mst")->insertGetId(array(
            "date" => now()
        ));

        foreach ($prod_list as $key => $value) {

            Db::table("direct_inward_det")->insertGetId(array(
                "mst_id" => $mst_id,
                "product_id" => $value->product_id,
                "qty" => $value->qty,
            ));


            $finish_product_stock = DB::table("finish_product_stock")->where("product_id", $value->product_id)->first();
            if ($finish_product_stock) {
                DB::table("finish_product_stock")->where("product_id", $value->product_id)->increment("stock", $value->qty);
            } else {
                DB::table("finish_product_stock")->insertGetId(array(
                    "product_id" => $value->product_id,
                    "stock" => $value->qty,
                ));
            }
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function DirectInwardChallan(Request $request)
    {
        $date = request("date", date("Y-m-d"));
        $direct_inward_mst = DB::table("direct_inward_mst")->where("date", $date)->first();

        $direct_inward_det = collect();
        if ($direct_inward_mst) {


            $direct_inward_det = DB::table("direct_inward_det as a")
                ->select("a.*", "b.name", "c.name as sub_category")
                ->join("finish_products_mst as b", "a.product_id", "b.id")
                ->join("f_product_sub_category as c", "b.f_sub_category_id", "c.id")
                ->where("a.mst_id", $direct_inward_mst->id)
                ->get();
        }
        return view("direct-inward-challan", compact("direct_inward_mst", "direct_inward_det"));
    }

    public function DeletePOProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',



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
            DB::table("po_det")->where("id", $request->id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function updatePOProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mst_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required',


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

        $gst_type = "";
        $po_mst = DB::table("po_mst")->where("id", $request->mst_id)->first();
        $vendor = DB::table("vendor")->where("id", $po_mst->vendor_id)->first();
        $products = DB::table("products")->where("id", $request->product_id)->first();
        $company_setting = DB::table("company_settings")->where("id", 1)->first();
        if ($vendor->gst) {
            $gst_number = substr($vendor->gst, 0, 2);
            $company_gst_no = substr($company_setting->gst_no, 0, 2);
            if ($gst_number == $company_gst_no) {
                $gst_type = "Inner GST";
            } else {
                $gst_type = "Outer GST";
            }
        } else {
            $gst_type = "Outer GST";
        }


        try {
            $exist = DB::table('po_det')
                ->where('mst_id', $request->mst_id)
                ->where('id', $request->pid)
                ->first();



            if ($exist) {
                DB::table('po_det')
                    ->where('id', $request->pid)

                    ->update([
                        'qty' => $request->qty,
                        'price' => $request->price,
                    ]);
            } else {
                // DB::table('po_det')->insertGetId(array(
                //     "mst_id" => $request->mst_id,
                //     "product_id" => $request->product_id,
                //     "qty" => $request->qty,
                //     "price" => $products->price,
                //     "gst" => $products->gst,
                //     "gst_type" => $gst_type,
                //     "type" => $request->productType,

                // ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }


    public function AddPOProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mst_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required',


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

        $gst_type = "";
        $po_mst = DB::table("po_mst")->where("id", $request->mst_id)->first();
        $vendor = DB::table("vendor")->where("id", $po_mst->vendor_id)->first();
        $products = DB::table("products")->where("id", $request->product_id)->first();
        $company_setting = DB::table("company_settings")->where("id", 1)->first();
        if ($vendor->gst) {
            $gst_number = substr($vendor->gst, 0, 2);
            $company_gst_no = substr($company_setting->gst_no, 0, 2);
            if ($gst_number == $company_gst_no) {
                $gst_type = "Inner GST";
            } else {
                $gst_type = "Outer GST";
            }
        } else {
            $gst_type = "Outer GST";
        }


        try {
            $exist = DB::table('po_det')
                ->where('mst_id', $request->mst_id)
                ->where('product_id', $request->product_id)
                ->where('type', $request->productType)
                ->first();



            if ($exist) {
                return redirect()->back()->with('error', "Already Exists");
            } else {
                DB::table('po_det')->insertGetId(array(
                    "mst_id" => $request->mst_id,
                    "product_id" => $request->product_id,
                    "qty" => $request->qty,
                    "price" => $products->price,
                    "gst" => $products->gst,
                    "gst_type" => $gst_type,
                    "type" => $request->productType,

                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }


    public function GeneratePOFinishGoods(Request $request)
    {

        $vendor = DB::table("vendor")->orderBy("name", "asc")->get();

        $category = DB::table("f_product_category")->get();
        return view("generate-po-finish-goods", compact("vendor", "category"));
    }

    public function GetFinishProducts(Request $request)
    {
        return DB::table("finish_products_mst")->where("f_category_id", $request->id)->get();
    }

    public function SaveFinishPO(Request $request)
    {
        $po_id = 'PO_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'name' => 'required',

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
        $gst_type = "";
        $vendor = DB::table("vendor")->where("id", $request->vendor_id)->first();
        $company_setting = DB::table("company_settings")->where("id", 1)->first();
        if ($vendor->gst) {
            $gst_number = substr($vendor->gst, 0, 2);
            $company_gst_no = substr($company_setting->gst_no, 0, 2);
            if ($gst_number == $company_gst_no) {
                $gst_type = "Inner GST";
            } else {
                $gst_type = "Outer GST";
            }
        } else {
            $gst_type = "Outer GST";
        }


        $status = "pending";
        if ($request->user->id == 1) {
            $status = "generated";
        }
        DB::beginTransaction();
        try {
            $mst_id = DB::table('po_mst_finish_goods')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "user_id" => $request->user->id,
                "po_id" => $po_id,
                "name" => $request->name,
                "description" => $request->description,
                "status" => $status,

            ));



            foreach ($prod_list as $key => $value) {
                DB::table('po_det_finish_goods')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "gst" => $value->gst,
                    "gst_type" => $gst_type,
                    "cess_tax" => $value->cess_tax

                ));
            }
            DB::commit();

            return redirect("purchase-order-view-finish-products/" . $mst_id)->with('success', "Save Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function PurchaseOrderFInishGoods(Request $request, $status)
    {
        $po = $request->input('po', 1);
        $where = [];

        if ($po == 2) {
            $where[] = ['a.order_id', '=', 0];
        }
        if ($po == 3) {
            $where[] = ['a.order_id', '>', 0];
        }



        $po = DB::table("po_mst_finish_goods as a")
            ->select("a.*", "b.name as vendor_name", "c.name as user_name")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")

            ->where("a.status", $status)
            ->where($where);
        if (request("fromDt")) {
            $po->whereDate("a.created_at", ">=", request("fromDt"));
        }
        if (request("toDt")) {
            $po->whereDate("a.created_at", "<=", request("toDt"));
        }

        $po_mst = $po->whereIn("a.user_id", $request->userIds)
            ->orderBy("a.id", "desc")
            ->get();
        return view("purchase-order-finish-goods", compact("po_mst", "status"));
    }

    public function InwardStockFinishGoods(Request $request)
    {
        $vendor = DB::table("vendor")

            ->orderBy("name", "asc")
            ->get();
        $store = DB::table("store")->get();
        return view("inward-stock-finish-goods", compact("vendor", "store"));
    }

    public function GetPOFinishGoods(Request $request)
    {
        $po_mst = DB::table('po_mst_finish_goods')
            ->where('vendor_id', $request->id)
            ->where(function ($query) {
                $query->where('status', 'partial')
                    ->orWhere('status', 'generated');
            })
            ->get();
        return $po_mst;
    }

    public function GetPODetFinishGoods(Request $request)
    {
        $po_det = DB::table("po_det_finish_goods as a")
            ->select("a.*", "b.name as product_name", "b.article_no", "b.id as product_id")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->whereIn("mst_id", $request->id)->get();
        return $po_det;
    }

    public function SaveInwardStockFinishGoods(Request $request)
    {
        $inward_id = 'Inward_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'po_id' => 'required',


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
        $poGrouped = collect($prod_list)->groupBy('po_id');
        // echo "<pre>";
        // print_r($poGrouped);

        DB::beginTransaction();


        try {

            foreach ($poGrouped as $po_id => $products) {
                $mst_id = DB::table('stock_inward_mst_finish_goods')->insertGetId(array(
                    "vendor_id" => $request->vendor_id,
                    "po_id" => $po_id,

                    "invoice_no" => $request->invoice_no,
                    "invoice_date" => $request->invoice_date,
                    "received_material_date" => $request->received_material_date,
                    "description" => $request->description,
                    "user_id" => $request->user->id,

                ));
                $status = 0;
                foreach ($products as $value) {
                    $check_po_det = DB::table('po_det_finish_goods')->where("id", $value->po_det_id)->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->first();
                    if ($check_po_det) {


                        $det_id = DB::table('stock_inward_det_finish_goods')->insertGetId(array(
                            "mst_id" => $mst_id,
                            "product_id" => $value->product_id,
                            "qty" => $value->qty,
                            "price" => $value->price,
                            "gst" => $value->gst,
                            "cess_tax" => $check_po_det->cess_tax,

                        ));


                        DB::table('po_det_finish_goods')->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->increment("received_qty", $value->qty);

                        $current_stock = DB::table("finish_product_stock")->where("product_id", $value->product_id)->first();

                        if ($current_stock) {
                            DB::table('finish_product_stock')->where("id", $current_stock->id)->update([
                                'stock' => DB::raw('stock + ' . $value->qty)
                            ]);
                        } else {
                            DB::table('finish_product_stock')->insertGetId(array(

                                "product_id" => $value->product_id,
                                "stock" => $value->qty,
                            ));
                        }

                        $po_det = DB::table('po_det_finish_goods')->where("mst_id", $value->po_id)->where("product_id", $value->product_id)->first();

                        if ($po_det->received_qty < $po_det->qty) {
                            $status = 1;
                        }
                        if ($status == 1) {
                            DB::table('po_mst_finish_goods')->where("id", $value->po_id)->update(array(
                                "status" => "partial",
                            ));
                        } else {
                            DB::table('po_mst_finish_goods')->where("id", $value->po_id)->update(array(
                                "status" => "complete",
                            ));
                        }
                    }
                }
            }
            //die;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect("inward-challan-finish-goods-view/$mst_id")->with('success', "Save Successfully");
    }

    public function PurchaseOrderViewFinishProducts(Request $request, $id)
    {
        $po_mst = DB::table("po_mst_finish_goods as a")
            ->select("a.*", "b.name as vendor_name", "b.number as vendor_number", "b.email as vendor_email", "b.address as vendor_address", "b.state as vendor_state", "b.city as vendor_city", "b.pincode as vendor_pincode", "b.gst as vendor_gst", "b.company_name")
            ->join("vendor as b", "a.vendor_id", "b.id")

            ->where("a.id", $id)->first();
        $po_det = DB::table("po_det_finish_goods as a")
            ->select("a.*", "b.name as product_name", "c.name as sub_category")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->join("f_product_sub_category as c", "b.f_sub_category_id", "c.id")
            ->where("mst_id", $po_mst->id)
            ->get();


        $products = DB::table("products as a")
            ->select("a.*")
            ->join("vendor_product as b", "a.id", "b.product_id")
            ->where("b.vendor_id", $po_mst->vendor_id)
            ->where("a.active", 1)->get();

        return view("purchase-order-view-finish-products", compact("po_mst", "po_det", "products"));
    }

    public function VendorProductFinishGoods(Request $request, $id)
    {


        $vendor_product = DB::table("finish_products_mst as a")
            ->select("a.*", "b.name as category", "c.name as sub_category")
            ->join("f_product_category as b", "a.f_category_id", "b.id")
            ->join("f_product_sub_category as c", "a.f_sub_category_id", "c.id")
            ->join("vendor_product_finish_goods as e", "a.id", "e.product_id")
            ->where("e.vendor_id", $id)
            ->get();

        $vendor = DB::table("vendor")->where("id", $id)->first();
        $products = DB::table("finish_products_mst as a")
            ->select("a.*", "b.name as category", "c.name as sub_category")
            ->leftJoin("f_product_category as b", "a.f_category_id", "b.id")
            ->leftJoin("f_product_sub_category as c", "a.f_sub_category_id", "c.id")
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from("vendor_product_finish_goods as d")
                    ->whereColumn("d.product_id", "a.id")
                    ->where("d.vendor_id", $id);
            })
            ->where("a.f_category_id", 2)
            ->get();



        return view("vendor-product-finish-goods", compact("vendor_product", "vendor", "products"));
    }

    public function AllocateFinishGoodsProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'product_id' => 'required',

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

        foreach ($request->product_id as $key => $value) {


            DB::table('vendor_product_finish_goods')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "product_id" => $value,
            ));
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function GetVendorFinishProducts(Request $request)
    {
        $products = DB::table("finish_products_mst as a")
            ->select("a.*")
            ->join("vendor_product_finish_goods as b", "a.id", "b.product_id")
            ->where("b.vendor_id", $request->id)
            ->where("a.active", 1)
            ->orderBy("a.name", "asc")
            ->get();
        return $products;
    }

    public function inwardChallanFG(Request $request)
    {

        $data = stock_inward_mst_finish_goods::with(["vendorDetails", "poDetails"])
            ->when(request("fromDt"), function ($q) {
                $q->whereDate("invoice_date", ">=", request("fromDt"));
            })
            ->when(request("toDt"), function ($q) {
                $q->whereDate("invoice_date", "<=", request("toDt"));
            })
            ->orderBy("id", "desc")
            ->get();

        return view("inward-challan-finish-goods", compact("data"));
    }

    public function inwardChallanFGView(Request $request, $id)
    {
        $po_mst = stock_inward_mst_finish_goods::with(["vendorDetails", "poDetails"])->where("id", $id)->first();
        $po_det = stock_inward_det_finish_goods::with(["productDetails"])->where("mst_id", $id)->get();

        return view("inward-challan-finish-goods-view", compact("po_mst", "po_det"));
    }

    public function deletePO(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',



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


            DB::table("po_mst")->where("id", $request->id)->delete();
            DB::table("po_det")->where("mst_id", $request->id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }
}
