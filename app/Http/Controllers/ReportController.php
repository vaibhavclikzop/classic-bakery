<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\FlareClient\View;

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
    public function SaleRegisterReport(Request $request)
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
        $order_type = DB::table("order_type")->get();

        return view("report.production-chart-report", compact("category", "order_type"));
    }

    public function productionChartReportData(Request $request)
    {
        $date = $request->date ?? date("Y-m-d");
        $category_id = $request->category_id;
        $customer_type = $request->customer_type;
        $order_type = $request->order_type;

        $page = $request->page ?? 1;
        $limit = 1000;
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
        if ($customer_type) {
            $query->where("b.order_type", $customer_type);
        }
        if ($order_type) {
            $query->where("b.order_type_id", $order_type);
        }

        $data = $query
            ->groupBy("f.name", "e.name", "d.name")
            ->orderBy("f.name")
            ->orderBy("e.name")
       
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function FaStockUploadReport()
    {
        return view("report.fa-stock-upload-report");
    }

    public function getFaStockReportData(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt   = $request->input("toDt") ?: Carbon::now()->toDateString();

        $page = $request->page ?? 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $data = DB::select("
        SELECT 
            a.id AS upload_code,
            a.date AS upload_date,

            c.name AS category_name,
            sc.name AS sub_category_name,

            p.name AS item_name,
            u.name AS uom,

            b.inward_qty AS qty

        FROM finish_inward_mst a

        JOIN finish_inward_det b 
            ON a.id = b.mst_id

        LEFT JOIN products p 
            ON b.product_id = p.id

        LEFT JOIN category c 
            ON p.category_id = c.id

        LEFT JOIN sub_category sc 
            ON p.sub_category_id = sc.id

        LEFT JOIN unit_type u 
            ON p.uom = u.id

        WHERE a.date BETWEEN ? AND ?

        ORDER BY a.date DESC

        LIMIT ? OFFSET ?
    ", [$fromDt, $toDt, $limit, $offset]);

        return response()->json(['data' => $data]);
    }
    public function manualOrderReport()
    {
        return view('report.manual-order-report');
    }


    public function purchaseRegisterTaxBifurcation(Request $request)
    {
        $fromDt = request("fromDt");
        $toDt = request("toDt");

        $gstRatesRaw = DB::table('gst')->orderBy("gst", "asc")->pluck('gst')->toArray();
        $gstRates = array_map(fn($gst) => (int)$gst, $gstRatesRaw);

        $columnsPurchase = [];

        foreach ($gstRates as $gst) {

            // ✅ TAXABLE (base amount)
            $columnsPurchase[] = "
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN (b.qty * b.price)
                ELSE 0 
            END
        ) AS taxable_$gst
    ";

            // ✅ GST
            $columnsPurchase[] = "
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN ROUND((b.qty * b.price * $gst) / 100, 2)
                ELSE 0 
            END
        ) AS gst_$gst
    ";

            // ✅ CESS (if applicable)
            $columnsPurchase[] = "
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN ROUND((b.qty * b.price * b.cess_tax) / 100, 2)
                ELSE 0 
            END
        ) AS cess_$gst
    ";

            // ✅ TOTAL (taxable + gst + cess)
            $columnsPurchase[] = "
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN 
                    (b.qty * b.price) 
                    + ROUND((b.qty * b.price * $gst) / 100, 2)
                    + ROUND((b.qty * b.price * b.cess_tax) / 100, 2)
                ELSE 0 
            END
        ) AS total_$gst
    ";
        }


        $dynamicColumnsPurchase = implode(",\n", $columnsPurchase);

        $data = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->selectRaw("
        a.invoice_id,
        a.received_material_date as invoice_date,
        c.name as vendor,
        a.delivery_charges,
        a.status,
        $dynamicColumnsPurchase,

        -- TOTAL TAXABLE
        SUM(b.qty * b.price) as total_taxable,

        -- TOTAL GST
        SUM((b.qty * b.price * b.gst) / 100) as total_gst,

        -- TOTAL CESS
        SUM((b.qty * b.price * b.cess_tax) / 100) as total_cess,

        -- GRAND TOTAL
        SUM(
            (b.qty * b.price)
            + ((b.qty * b.price * b.gst) / 100)
            + ((b.qty * b.price * b.cess_tax) / 100)
        ) as grand_total
    ")
            ->whereBetween("a.received_material_date", [$fromDt, $toDt])
            ->groupBy("a.id", "a.invoice_id", "a.invoice_date", "c.name", "a.delivery_charges","a.received_material_date","a.status")
            ->get();

        return view("report.purchase-register-tax-bifurcation", compact("data", "gstRatesRaw", "gstRates"));
    }
}
