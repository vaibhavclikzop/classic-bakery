<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function rmConsumptionReport(Request $request)
    {
        return view("report.rm-consumption-report");
    }
    public function getRmConsumptionReportData(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page = $request->page ?? 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;
        $data = DB::select("SELECT a.id AS mi_code, a.invoice_date AS mi_date,
        c.name AS category_name,
        p.name AS item_name,
        u.name AS uom,

        b.qty AS mi_qty,

        s.price AS last_price,
        (b.qty * s.price) AS amount,

        'RM MAIN STORE' AS from_location,
        d.name AS to_location

        FROM outward_mst a
        JOIN outward_det b ON a.id = b.mst_id

        LEFT JOIN products p ON b.product_id = p.id
        LEFT JOIN category c ON p.category_id = c.id
        LEFT JOIN unit_type u ON p.uom = u.id

        LEFT JOIN (
            SELECT product_id, MAX(price) price
            FROM stock_inward_det
            GROUP BY product_id
        ) s ON s.product_id = b.product_id

        LEFT JOIN department d ON a.department_id = d.id

        WHERE a.invoice_date >= ?
        AND a.invoice_date <= ?

        ORDER BY a.invoice_date DESC

        LIMIT ? OFFSET ?
    ", [$fromDt, $toDt, $limit, $offset]);

        return response()->json(['data' => $data]);
    }



    public function SubReportConsumption(Request $request)
    {
        return view("report.sub-report-consumption");
    }
    public function getSaleRegisterReportData(Request $request)
    {

        $fromDt = $request->fromDt;
        $toDt   = $request->toDt;

        $page = $request->page ?? 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $data = DB::select(" SELECT fc.name AS category_group, fsc.name AS category_name, p.name AS item_name,
            SUM(od.qty) AS qty,
            SUM(od.qty * od.price) AS value_sold,
            SUM(od.qty) AS qty_after_gvn,
            SUM(od.qty * od.price) AS value_after_gvn,
            COALESCE(c.address,'Counter Sale') AS address
            FROM order_mst om

            JOIN order_det od 
                ON om.id = od.mst_id

            JOIN products p 
                ON od.product_id = p.id

            LEFT JOIN order_type ot
                ON om.order_type_id = ot.id

            LEFT JOIN f_product_sub_category fsc
                ON ot.f_sub_category_id = fsc.id

            LEFT JOIN f_product_category fc
                ON fsc.f_category_id = fc.id

            LEFT JOIN customers c
                ON om.customer_id = c.id

            WHERE om.delivery_date BETWEEN ? AND ?

            GROUP BY 
                address,
                fc.name,
                fsc.name,
                p.id,
                p.name

            ORDER BY address, fc.name, fsc.name

            LIMIT ? OFFSET ?

    ", [$fromDt, $toDt, $limit, $offset]);

        return response()->json(['data' => $data]);
    }
    public function productionChartReport()
    {
        $category = DB::table("f_product_category")->get();

        return view("report.production-chart-report", compact("category"));
    }

    public function productionChartReportData(Request $request)
    {
        $date = $request->date ?? date("Y-m-d");
        $category_id = $request->category_id;

        $page = $request->page ?? 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $query = DB::table("work_order_det as a")
            ->select(
                "f.name as category",
                "e.name as sub_category",
                "d.name as product",
                DB::raw("SUM(a.qty) as qty")
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("finish_products_mst as d", "a.product_id", "d.id")
            ->join("f_product_sub_category as e", "d.f_sub_category_id", "e.id")
            ->join("f_product_category as f", "d.f_category_id", "f.id")
            ->whereDate("b.delivery_date", $date);

        if ($category_id) {
            $query->where("f.id", $category_id);
        }

        $data = $query
            ->groupBy("f.name", "e.name", "d.name")
            ->orderBy("f.name")
            ->orderBy("e.name")
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
}
