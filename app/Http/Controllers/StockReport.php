<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class StockReport extends Controller
{
    public function CurrentStock(Request $request)
    {
        $location = $request->input("location");
        $search = $request->input("search");
        $where = "";

        $where = DB::table("current_stock as a")
            ->select("a.*", "b.name as product", "b.article_no", "b.id as product_id")
            ->rightJoin("products as b", "a.product_id", "b.id");


        if ($search) {
            $where->where("b.name", 'like', '%' . $search . '%');
        }
        $current_stock = $where->paginate(50);


        $user_type = $request->user->user_type;

        return view("current-stock", compact("current_stock", "user_type"));
    }


    public function CurrentStockFinishProducts(Request $request)
    {
        $location = $request->input("location");
        $where = "";

        $where = DB::table("finish_product_stock as a")
            ->select("a.*", "b.name as product")
            ->join("finish_products_mst as b", "a.product_id", "b.id");

        if ($location) {
            $where->where("a.location_id", $location);
        }
        $current_stock = $where->get();

        $location = DB::table("store")->get();
        return view("current-stock-finish-products", compact("current_stock", "location"));
    }

    public function NearMinimumStock(Request $request)
    {

        $location = $request->input("location");
        $where = "";

        $where = DB::table("current_stock as a")
            ->select("a.*", "b.name as product",  "b.article_no", "b.min_stock", "c.name as sub_category")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("sub_category as c", "b.sub_category_id", "=", "c.id")

            ->whereRaw("a.stock <= b.min_stock");
        if ($location) {
            $where->where("a.location_id", $location);
        }
        $current_stock = $where->get();
        $location = DB::table("store")->get();
        return view("near-by-minimum-stock", compact("current_stock", "location"));
    }


    public function InwardReport(Request $request)
    {
        $fromDt = request("fromDt");
        $toDt = request("toDt");

        $filter =   DB::table("stock_inward_mst as a")
            ->select("a.*", "b.name as vendor", "c.name as po_name", "e.name as user")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("po_mst as c", "a.po_id", "c.id")

            ->join("users as e", "a.user_id", "e.id");
        if ($fromDt) {
            $filter->whereDate("a.received_material_date", ">=", $fromDt);
        }

        if ($toDt) {
            $filter->whereDate("a.received_material_date", "<=", $toDt);
        }
        $stock_inward_mst = $filter->orderBy("a.id", "desc")->get();
        return view("inward-report", compact("stock_inward_mst"));
    }

    public function InwardReportView(Request $request, $id)
    {

        $stock_inward_mst =   DB::table("stock_inward_mst as a")
            ->select("a.*", "b.name as vendor", "c.name as po_name",  "e.name as user")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("po_mst as c", "a.po_id", "c.id")

            ->join("users as e", "a.user_id", "e.id")
            ->where("a.id", $id)
            ->first();


        $stock_inward_det = DB::table("stock_inward_det as a")
            ->select("a.*", "b.name as product_name", "b.article_no")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.type", "raw material")
            ->where("a.mst_id", $id)
            ->get();





        $RM = DB::table("stock_inward_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci  as product_name, b.article_no, b.id as product_id")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.type", "raw material")
            ->where("a.mst_id", $id);

        $FG = DB::table("stock_inward_det as a")
            ->selectRaw("a.*, b.name COLLATE utf8mb4_unicode_ci as product_name, b.article_no, b.id as product_id")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.type", "finished product")
            ->where("a.mst_id", $id);
        $stock_inward_det = $RM->union($FG)->get();



        return view("inward-report-view", compact("stock_inward_mst", "stock_inward_det"));
    }

    public function AttendanceReport(Request $request)
    {

        $users = DB::table("users")->whereIn("id", $request->userIds)->get();

        $year = request()->input("year", date("Y"));
        $month = request()->input("month", date("m"));
        $user_id = request()->input("emp_id", $request->user->id);



        $attendance_report = DB::table("attendance")->where("emp_id", $user_id)->get();



        return view("attendance-report", compact("users", "attendance_report"));
    }

    public function AttendanceReportMonthly(Request $request)
    {

        $year = request()->input("year", date("Y"));
        $month = request()->input("month", date("m"));

        $sdate = $year . '-' . $month . '-01';
        $sdate1 = $year . '-' . $month . '-01';


        if ($month == 12) {
            $month = '01';
            $year = (int)$year + 1;
        } else $month = str_pad((int)$month + 1, 2, '0', STR_PAD_LEFT);

        $edate = $year . '-' . $month . '-01';
        $edate1 = $year . '-' . $month . '-01';

        $month_dates = [];
        $attendance_monthly = [];
        $employees = DB::table("users")->whereIn("id", $request->userIds)->get();
        foreach ($employees as $emp) {
            $emp_data = [];
            $sdate = $sdate1;

            $emp_data[] = array('col' => $emp->name);

            while (strtotime($sdate)  < strtotime($edate)) {


                $attendance_report = DB::table("attendance")->where("emp_id", $emp->id)->whereDate("start_time", $sdate)->first();
                $hours = 0;
                if ($attendance_report) {

                    if (!empty($attendance_report->end_time)) {

                        $hours = round((strtotime($attendance_report->end_time) - strtotime($attendance_report->start_time)) / 3600, 2);
                    } else
                        $hours = 1;
                }

                $emp_data[] = array('col' => $sdate, 'hours' => $hours);
                $sdate = date('Y-m-d', strtotime($sdate . ' +1 day'));
            }
            $attendance_monthly[] = $emp_data;
        }

        return view("attendance-report-monthly", compact("attendance_monthly", "sdate1", "edate1"));
    }


    public function GetCSProducts(Request $request)
    {
        $location = DB::table("current_stock as a")
            ->select("a.*", "b.name", "b.article_no")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->where("a.location_id", $request->id)->get();
        return $location;
    }



    public function AuditSetting(Request $request)
    {
        $category_id = request("category_id");
        $sub_category_id = request("sub_category_id");
        $category = DB::table("category")->get();
        $sub_category = collect();
        if ($category_id) {
            $sub_category = DB::table("sub_category")->where("category_id", $category_id)->get();
        }

        $data = DB::table("current_stock as a")
            ->select("a.*", "b.name", "b.article_no", "b.id as product_id")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->where("b.category_id", $category_id);

        if ($sub_category_id) {
            $data->where("b.sub_category_id", $sub_category_id);
        }
        $products =  $data->get();


        return view("audit-setting", compact("category", "sub_category", "products"));
    }


    public function SaveAuditReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'check' => 'required',


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

            $mst_id =  DB::table('audit_report_mst')->insertGetId(array(
                "date" => now(),
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,
                "category_id" => $request->category_id,

            ));


            foreach ($request->check as $key => $value) {

                $current_stock =  DB::table('current_stock')->where('id', $value)->first();

                DB::table('audit_report_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $current_stock->product_id,
                    "category_id" => $request->category_id,
                    "current_stock" => $current_stock->stock,


                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }


    public function AuditReport(Request $request)
    {
        $audit_report_mst = DB::table("audit_report_mst as a")
            ->select("a.*", "b.name", "c.name as category")
            ->join("users as b", "a.user_id", "=", "b.id")
            ->join("category as c", "a.category_id", "=", "c.id")
            ->orderBy("a.id", "desc")
            ->get();
        return view("audit-report", compact("audit_report_mst"));
    }

    public function AuditReportView(Request $request, $id)
    {
        $audit_report_det =   DB::table("audit_report_det as a")
            ->select("a.*", "b.name as product", "c.name as category", "d.name as user")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("category as c", "a.category_id", "=", "c.id")
            ->leftJoin("users as d", "a.user_id", "=", "d.id")
            ->where("a.mst_id", $id)
            ->get();
        return view("audit-report-view", compact("audit_report_det"));
    }

    public function SaveAudit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'stock' => 'required',


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

            DB::table('audit_report_det')->where("id", $request->id)->update(array(
                "stock" => $request->stock,
                "user_id" => $request->user->id,
                "status" => "audit",
            ));

            $audit_report_det = DB::table("audit_report_det")->where("id", $request->id)->first();
            $audit_report_mst =  DB::table("audit_report_det")->where("mst_id", $audit_report_det->mst_id)->where("status", "pending")->first();
            if (!$audit_report_mst) {
                DB::table("audit_report_mst")->where("id", $audit_report_det->mst_id)->update(array(
                    "status" => "complete"
                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }

    public function SaveStock(Request $request)
    {

        $validator = Validator::make($request->all(), [
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
        try {
            $id = 0;
            $total_stock = 0;
            if ($request->id) {
                 $id = $request->id;
                $stock = DB::table('current_stock')->where('id', $request->id)->first();

                if(!$stock || $stock->stock + $request->qty < 0){
                    return response()->json(['error' => 'Stock cannot be negative'], 400);
                }
                DB::table('current_stock')->where("id", $request->id)->increment("stock", $request->qty);               
                $total_stock = DB::table('current_stock')->where('id', $request->id)->value('stock');

            } else {
                $mst_id = DB::table('current_stock')->insertGetId(array(
                    "product_id" => $request->product_id,
                    "stock" => $request->qty,
                ));
                $id = $mst_id;
            }


            DB::table('stock_adjustment')->insertGetId(array(

                "cs_id" => $id,
                "qty" => $request->qty,

            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return response()->json(['total_stock' => $total_stock]);
    }

    public function GetStockAdjustmentHistory(Request $request)
    {
        $stock_adjustment =  DB::table("stock_adjustment")->where("cs_id", $request->id)->get();
        return $stock_adjustment;
    }
    public function GetFPStockAdjustmentHistory(Request $request)
    {
        $stock_adjustment =  DB::table("fp_stock_adjustment")->where("cs_id", $request->id)->get();
        return $stock_adjustment;
    }

    public function FinishGoodsDefectiveStock(Request $request)
    {
        $data =   DB::table("finish_goods_defective_stock as a")
            ->select("a.*", "b.name as product")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->get();
        return view("finish-goods-defective-stock", compact("data"));
    }



    public function SaveFPStock(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
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


        try {
                $stock = DB::table('finish_product_stock')->where('id', $request->id)->first();

                if(!$stock || $stock->stock + $request->qty < 0){
                    return response()->json(['error' => 'Stock cannot be negative'], 400);
                }
            DB::table('finish_product_stock')->where("id", $request->id)->increment("stock", $request->qty);
                $total_stock = DB::table('finish_product_stock')->where('id', $request->id)->value('stock');

            DB::table('fp_stock_adjustment')->insertGetId(array(

                "cs_id" => $request->id,
                "qty" => $request->qty,

            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return response()->json(['total_stock' => $total_stock]);
    }


    public function CurrentStockOutlet(Request $request)
    {
        $outlet_id = request("outlet_id", 0);
        $outlet =   DB::table("outlet")->get();
        $current_stock =  DB::table("outlet_current_stock as a")
            ->select("a.*", "b.name as product")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->where("a.outlet_id", $outlet_id)->get();
        return view("outlet-current-stock", compact("outlet", "current_stock"));
    }

    public function FGAuditSetting(Request $request)
    {
        $category_id = request("category_id");
        $sub_category_id = request("sub_category_id");
        $category = DB::table("f_product_category")->get();
        $sub_category = collect();
        if ($category_id) {
            $sub_category = DB::table("f_product_sub_category")->where("f_category_id", $category_id)->get();
        }

        $data = DB::table("finish_product_stock as a")
            ->select("a.*", "b.name", "b.article_no", "b.id as product_id")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->where("b.f_category_id", $category_id);

        if ($sub_category_id) {
            $data->where("b.f_sub_category_id", $sub_category_id);
        }
        $products =  $data->get();


        return view("fg-audit-setting", compact("category", "sub_category", "products"));
    }

    public function SaveFPAuditReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'check' => 'required',


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

            $mst_id =  DB::table('fp_audit_report_mst')->insertGetId(array(
                "date" => now(),
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,
                "category_id" => $request->category_id,

            ));


            foreach ($request->check as $key => $value) {

                $current_stock =  DB::table('finish_product_stock')->where('id', $value)->first();

                DB::table('fp_audit_report_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $current_stock->product_id,
                    "category_id" => $request->category_id,
                    "current_stock" => $current_stock->stock,

                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }

    public function FGAuditReport(Request $request)
    {
        $audit_report_mst = DB::table("fp_audit_report_mst as a")
            ->select("a.*", "b.name", "c.name as category")
            ->join("users as b", "a.user_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->orderBy("a.id", "desc")
            ->get();
        return view("fg-audit-report", compact("audit_report_mst"));
    }

    public function FGAuditReportView(Request $request, $id)
    {
        $audit_report_det =   DB::table("fp_audit_report_det as a")
            ->select("a.*", "b.name as product", "c.name as category", "d.name as user")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->leftJoin("users as d", "a.user_id", "=", "d.id")
            ->where("a.mst_id", $id)
            ->get();
        return view("fg-audit-report-view", compact("audit_report_det"));
    }

    public function SaveFGAudit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'stock' => 'required',


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

            DB::table('fp_audit_report_det')->where("id", $request->id)->update(array(
                "stock" => $request->stock,
                "user_id" => $request->user->id,
                "status" => "audit",
            ));

            $audit_report_det = DB::table("fp_audit_report_det")->where("id", $request->id)->first();
            $audit_report_mst =  DB::table("fp_audit_report_det")->where("mst_id", $audit_report_det->mst_id)->where("status", "pending")->first();
            if (!$audit_report_mst) {
                DB::table("fp_audit_report_mst")->where("id", $audit_report_det->mst_id)->update(array(
                    "status" => "complete"
                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }

    public function OutletAuditSetting(Request $request)
    {

        $category_id = request("category_id");
        $outlet_id = request("outlet_id");
        $sub_category_id = request("sub_category_id");
        $category = DB::table("f_product_category")->get();
        $sub_category = collect();
        if ($category_id) {
            $sub_category = DB::table("f_product_sub_category")->where("f_category_id", $category_id)->get();
        }

        $data = DB::table("outlet_current_stock as a")
            ->select("a.*", "b.name", "b.article_no", "b.id as product_id")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->where("b.f_category_id", $category_id)
            ->where("a.outlet_id", $outlet_id);

        if ($sub_category_id) {
            $data->where("b.f_sub_category_id", $sub_category_id);
        }
        $products =  $data->get();
        $outlet = DB::table("outlet")->get();

        return view("outlet-audit-setting", compact("category", "sub_category", "products", "outlet"));
    }



    public function SaveOutletAuditReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'check' => 'required',
            'outlet_id' => 'required',


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

            $mst_id =  DB::table('outlet_audit_report_mst')->insertGetId(array(
                "date" => now(),
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,
                "category_id" => $request->category_id,
                "outlet_id" => $request->outlet_id,

            ));


            foreach ($request->check as $key => $value) {

                $current_stock =  DB::table('outlet_current_stock')->where('id', $value)->first();

                DB::table('outlet_audit_report_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $current_stock->product_id,
                    "category_id" => $request->category_id,
                    "current_stock" => $current_stock->stock,
                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }


    public function OutletAuditReport(Request $request)
    {
        $admin = DB::table("outlet_audit_report_mst as a")
            ->select("a.*", "b.name", "c.name as category", "d.outlet_name")
            ->join("users as b", "a.user_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->join("outlet as d", "a.outlet_id", "=", "d.id")
            ->where("a.type", "admin")
            ->orderBy("a.id", "desc");


        $outlet = DB::table("outlet_audit_report_mst as a")
            ->select("a.*", "b.name", "c.name as category", "d.outlet_name")
            ->join("outlet_users as b", "a.user_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->join("outlet as d", "a.outlet_id", "=", "d.id")
            ->where("a.type", "outlet")
            ->orderBy("a.id", "desc");
        $audit_report_mst = $admin->union($outlet)->get();
        return view("outlet-audit-report", compact("audit_report_mst"));
    }

    public function OutletAuditReportView(Request $request, $id)
    {
        $admin =   DB::table("outlet_audit_report_det as a")
            ->select("a.*", "b.name as product", "c.name as category", "d.name as user")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->leftJoin("users as d", "a.user_id", "=", "d.id")
            ->where("a.mst_id", $id)
            ->where("a.type", "admin");

        $outlet =   DB::table("outlet_audit_report_det as a")
            ->select("a.*", "b.name as product", "c.name as category", "d.name as user")
            ->join("finish_products_mst as b", "a.product_id", "=", "b.id")
            ->join("f_product_category as c", "a.category_id", "=", "c.id")
            ->leftJoin("users as d", "a.user_id", "=", "d.id")
            ->where("a.mst_id", $id)
            ->where("a.type", "outlet");
        $audit_report_det = $admin->union($outlet)->get();
        return view("outlet-audit-report-view", compact("audit_report_det"));
    }

    public function SaveOutletAudit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'stock' => 'required',


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

            DB::table('outlet_audit_report_det')->where("id", $request->id)->update(array(
                "stock" => $request->stock,
                "user_id" => $request->user->id,
                "status" => "audit",
                "type" => "admin",
            ));

            $audit_report_det = DB::table("outlet_audit_report_det")->where("id", $request->id)->first();
            $audit_report_mst =  DB::table("outlet_audit_report_det")->where("mst_id", $audit_report_det->mst_id)->where("status", "pending")->first();
            if (!$audit_report_mst) {
                DB::table("outlet_audit_report_mst")->where("id", $audit_report_det->mst_id)->update(array(
                    "status" => "complete"
                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }
}
