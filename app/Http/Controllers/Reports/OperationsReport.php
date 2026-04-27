<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsReport extends Controller
{
    public function departmentConsumptionReport(Request $request)
    {

        $department_id = request("department_id");
        $fromDt = request("fromDt") ?? date("Y-m-d");
        $toDt = request("toDt")  ?? date("Y-m-d");
        $department = DB::table('department')->get();
        $latestPurchase = DB::table("stock_inward_det")
            ->select("product_id", DB::raw("MAX(id) as last_id"))
            ->groupBy("product_id");

        $filter = DB::table("outward_mst as a")
            ->select(
                "b.product_id",
                "c.name",
                "c.price",
                DB::raw("COALESCE(sid.price, 'NA') as last_purchase_price"),
                DB::raw("SUM(b.qty) as qty")
            )
            ->join("outward_det as b", "a.id", "b.mst_id")
            ->join("products as c", "b.product_id", "c.id")


            ->leftJoinSub($latestPurchase, "latest", function ($join) {
                $join->on("b.product_id", "latest.product_id");
            })


            ->leftJoin("stock_inward_det as sid", "latest.last_id", "sid.id");

        if ($department_id) {
            $filter->where("a.department_id", $department_id);
        }

        if ($fromDt) {
            $filter->whereDate("a.invoice_date", ">=", $fromDt);
        }
        if ($toDt) {
            $filter->whereDate("a.invoice_date", "<=", $toDt);
        }
        $data = $filter
            ->groupBy("b.product_id", "c.name", "c.price", "sid.price")
            ->get();


        // echo "<pre>";
        // print_r($data);
        // die;
        return view("report.department-consumption-report", compact("department", "data"));
    }
}
