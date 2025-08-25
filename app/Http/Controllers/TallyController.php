<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallyController extends Controller
{
    public function tallyReport(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page = $request->input("page", 1);
        $limit = 1000;
        $offset = ($page - 1) * $limit;

        $sql = "
             SELECT
        a.invoice_no,
        a.id,
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
    GROUP BY a.invoice_no, a.invoice_date, a.id, e.name,c.gst

    UNION ALL

    SELECT
        a.invoice_no,
        a.id,
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
    GROUP BY a.invoice_no, a.invoice_date, a.id, e.outlet_name,c.gst

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

        // echo "<pre>";
        // print_r($data);
        // die;
        return view("tally-report", compact("data"));
    }
}
