<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallyController extends Controller
{
    public function tallyReport(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page = $request->input("page", 1);
        $limit = 1000;
        $offset = ($page - 1) * $limit;

        $sql = "
             SELECT
        a.invoice_no,
        a.order_no as id,
        a.invoice_date,
        e.name AS name,
        'Regular Order' AS order_type,
        'sales' AS invoice_type,
        c.gst AS gst,
        SUM(b.qty * c.price) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,
        ROUND(SUM(IF(c.gst_type = 'Outer GST', b.qty * c.price * c.gst / 100, 0)), 2) AS igst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST', b.qty * c.price * c.gst / 200, 0)), 2) AS cgst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST', b.qty * c.price * c.gst / 200, 0)), 2) AS sgst
    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN customers e ON d.customer_id = e.id
    WHERE d.order_type = 'customer'
      AND a.invoice_date BETWEEN ? AND ?
    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name,c.gst

    UNION ALL

    SELECT
        a.invoice_no,
        a.order_no as id,
        a.invoice_date,
        e.outlet_name AS name,
        'Regular Order' AS order_type,
            'sales' AS invoice_type,
            c.gst AS gst,
        SUM(b.qty * c.price) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,
        ROUND(SUM(IF(c.gst_type = 'Outer GST', b.qty * c.price * c.gst / 100, 0)), 2) AS igst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST', b.qty * c.price * c.gst / 200, 0)), 2) AS cgst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST', b.qty * c.price * c.gst / 200, 0)), 2) AS sgst
    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN outlet e ON d.customer_id = e.id
    WHERE d.order_type = 'outlet'
      AND a.invoice_date BETWEEN ? AND ?
    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name,c.gst

    UNION ALL

    SELECT
        a.id AS invoice_no,
        a.id,
        a.order_date AS invoice_date,
        c.name,
        'Advance Order' AS order_type,
            'sales' AS invoice_type,
        SUM(b.total_price) AS sub_total,
        SUM(b.mrp) AS total_mrp,
        0 AS cess_amt,
        0 AS igst,
        0 AS cgst,
        0 AS sgst,
        0 AS gst
    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id
    WHERE a.customer_type = 'customer' 
     AND (a.status = 'dispatch' OR a.status = 'delivered')
      AND a.order_date BETWEEN ? AND ?
    GROUP BY a.order_date, a.id, c.name

    UNION ALL

    SELECT
        a.id AS invoice_no,
        a.id,
        a.order_date AS invoice_date,
        c.outlet_name AS name,
        'Advance Order' AS order_type,
            'sales' AS invoice_type,
        SUM(b.total_price) AS sub_total,
        SUM(b.mrp) AS total_mrp,
        0 AS cess_amt,
        0 AS igst,
        0 AS cgst,
        0 AS sgst,
        0 AS gst
    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id
    WHERE a.customer_type = 'outlet'
      AND (a.status = 'dispatch' OR a.status = 'delivered')
      AND a.order_date BETWEEN ? AND ?
    GROUP BY a.order_date, a.id, c.outlet_name
";
        $params = [$fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt];

        $data = DB::select($sql . " ORDER BY invoice_date", array_merge($params));


        //             [invoice_no] => 
        //             [id] => 1
        //             [invoice_date] => 2025-09-02
        //             [name] => La Pizza Minia
        //             [order_type] => Regular Order
        //             [invoice_type] => sales
        //             [gst] => 0.00
        //             [sub_total] => 94.0000000000
        //             [total_mrp] => 150.0000000000
        //             [cess_amt] => 0.00000
        //             [igst] => 0.00
        //             [cgst] => 0.00
        //             [sgst] => 0.00

        $filterRawMaterial = DB::table("stock_inward_mst as a")
            ->select(
                DB::raw("a.invoice_id as invoice_no"),
                DB::raw("a.invoice_id as id"),
                DB::raw("a.received_material_date as invoice_date"),
                DB::raw("c.name as name"),
                DB::raw("'Regular Order' as order_type"),
                DB::raw("'purchase' as invoice_type"),


                DB::raw("SUM(b.qty * b.price) as sub_total"),
                DB::raw("b.gst as gst"),


                DB::raw("SUM((b.qty * b.price * b.cess_tax) / 100) as cess_amt"),
                DB::raw("SUM((b.qty * b.price * b.gst) / 100) as igst"),
                DB::raw("0 as cgst"),
                DB::raw("0 as sgst"),


                DB::raw("SUM((b.qty * b.price) + ((b.qty * b.price * b.gst)/100) + ((b.qty * b.price * b.cess_tax)/100)) as total_amount"),

                "a.delivery_charges"
            )
            ->join("stock_inward_det as b", "a.id", "=", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "=", "c.id");

        if ($fromDt) {
            $filterRawMaterial->whereDate("a.received_material_date", ">=", $fromDt);
        }

        if ($toDt) {
            $filterRawMaterial->whereDate("a.received_material_date", "<=", $toDt);
        }

        $rawMaterial = $filterRawMaterial
            ->groupBy("a.invoice_id", "a.received_material_date", "c.name", "a.delivery_charges", "b.gst")
            ->get();


        // echo "<pre>";
        // print_r($rawMaterial);
        // die;
        $data = array_merge($data, $rawMaterial->toArray());
        return view("tally-report", compact("data"));
    }


    public function debitCreditReport(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();

        $lastPriceSub = DB::table("stock_inward_det as si")
            ->select("si.product_id", "si.price", "si.gst", "si.cess_tax", "si.type")
            ->whereRaw("si.id = (select max(id) from stock_inward_det where product_id = si.product_id)");

        $purchase_return = DB::table("purchase_return_mst as a")
            ->join("purchase_return_det as b", "a.id", "=", "b.mst_id")
            ->joinSub($lastPriceSub, "c", function ($join) {
                $join->on("b.product_id", "=", "c.product_id");
                $join->on("b.type", "=", "c.type");
            })
            ->join("vendor as v", "a.vendor_id", "=", "v.id")
            ->select(
                "v.name as name",
                DB::raw("'purchase return' as invoice_type"),
                DB::raw("'purchase return' as order_type"),
                DB::raw("'0' as igst"),
                DB::raw("'0' as cgst"),
                DB::raw("'0' as sgst"),
                DB::raw("a.return_date as invoice_date"),
                DB::raw("a.id as id"),
                DB::raw("SUM(b.qty * c.price) as sub_total"),
                DB::raw("SUM((b.qty * c.price) * c.gst/100) as total_gst"),
                DB::raw("SUM((b.qty * c.price) * c.cess_tax/100) as cess_amt"),
                DB::raw("MAX(c.gst) as gst"),
                DB::raw("SUM(b.qty * c.price + (b.qty * c.price) * c.gst/100 + (b.qty * c.price) * c.cess_tax/100) as grand_total")
            )
            ->whereDate("a.return_date",">=",$fromDt)
            ->whereDate("a.return_date","<=",$toDt)
            ->groupBy("v.id", "v.name", "a.return_date", "a.id");


        $lastPriceSub = DB::table("finish_products_mst as f")
            ->select("f.id as product_id", "f.price", "f.gst", "f.cess_tax")
            ->whereRaw("f.id = (select max(id) from finish_products_mst where id = f.id)");

        $sale_return = DB::table("sale_return_mst as a")
            ->join("sale_return_det as b", "a.id", "b.mst_id")
            ->joinSub($lastPriceSub, "c", function ($join) {
                $join->on("b.product_id", "=", "c.product_id");
            })
            ->join("outlet as v", "a.customer_id", "v.id")
            ->select(
                "v.outlet_name as name",
                DB::raw("'sale return' as invoice_type"),
                DB::raw("'sale return' as order_type"),
                DB::raw("'0' as igst"),
                DB::raw("'0' as cgst"),
                DB::raw("'0' as sgst"),
                DB::raw("a.return_date as invoice_date"),
                DB::raw("a.id as id"),
                DB::raw("SUM(b.qty * c.price) as sub_total"),
                DB::raw("SUM((b.qty * c.price) * c.gst/100) as total_gst"),
                DB::raw("SUM((b.qty * c.price) * c.cess_tax/100) as cess_amt"),
                DB::raw("MAX(c.gst) as gst"),
                DB::raw("SUM(b.qty * c.price + (b.qty * c.price) * c.gst/100 + (b.qty * c.price) * c.cess_tax/100) as grand_total")
            )
                  ->whereDate("a.return_date",">=",$fromDt)
            ->whereDate("a.return_date","<=",$toDt)
            ->groupBy("v.id", "v.outlet_name", "a.return_date", "a.id");


                $data=$purchase_return->union($sale_return)->get();
        return view("debit-credit-note", compact("data"));

        dd($data);
    }
}
