<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallyController extends Controller
{
    public function tallyReportOld(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page = $request->input("page", 1);
        $limit = 1000;
        $offset = ($page - 1) * $limit;

        $sql = "
        SELECT
        a.status,
            a.invoice_no,
            a.order_no as id,
            a.invoice_date,
            e.name AS name,
            'Regular Order' AS order_type,
            '0.00' AS delivery_charges,
            'sales' AS invoice_type,
            c.gst AS gst,

            -- ✅ EXCLUSIVE SUB TOTAL
            ROUND(SUM((b.qty * c.price) / (1 + (c.gst / 100))), 2) AS sub_total,

            SUM(b.qty * c.mrp) AS total_mrp,
            SUM(c.cess_amt) AS cess_amt,

            -- ✅ IGST
            ROUND(SUM(
                IF(c.gst_type = 'Outer GST',
                    (b.qty * c.price) -
                    ((b.qty * c.price) / (1 + (c.gst / 100))),
                0)
            ), 2) AS igst,

    -- ✅ CGST
    ROUND(SUM(
        IF(c.gst_type = 'Inner GST',
            (
                (b.qty * c.price) -
                ((b.qty * c.price) / (1 + (c.gst / 100)))
            ) / 2,
        0)
    ), 2) AS cgst,

    -- ✅ SGST
    ROUND(SUM(
        IF(c.gst_type = 'Inner GST',
            (
                (b.qty * c.price) -
                ((b.qty * c.price) / (1 + (c.gst / 100)))
            ) / 2,
        0)
    ), 2) AS sgst

FROM outward_customer_order_mst a
JOIN outward_customer_order_det b ON a.id = b.mst_id
JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
JOIN order_mst d ON a.order_id = d.id
JOIN customers e ON d.customer_id = e.id

WHERE d.order_type = 'customer'
AND a.invoice_date BETWEEN ? AND ?

GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name, c.gst,a.status

UNION ALL

SELECT
a.status,
    a.invoice_no,
    a.order_no as id,
    a.invoice_date,
    e.outlet_name AS name,
    'Regular Order' AS order_type,
        '0.00' AS delivery_charges,
    'sales' AS invoice_type,
    c.gst AS gst,

    ROUND(SUM((b.qty * c.price) / (1 + (c.gst / 100))), 2) AS sub_total,

    SUM(b.qty * c.mrp) AS total_mrp,
    SUM(c.cess_amt) AS cess_amt,

    ROUND(SUM(
        IF(c.gst_type = 'Outer GST',
            (b.qty * c.price) -
            ((b.qty * c.price) / (1 + (c.gst / 100))),
        0)
    ), 2) AS igst,

    ROUND(SUM(
        IF(c.gst_type = 'Inner GST',
            (
                (b.qty * c.price) -
                ((b.qty * c.price) / (1 + (c.gst / 100)))
            ) / 2,
        0)
    ), 2) AS cgst,

    ROUND(SUM(
        IF(c.gst_type = 'Inner GST',
            (
                (b.qty * c.price) -
                ((b.qty * c.price) / (1 + (c.gst / 100)))
            ) / 2,
        0)
    ), 2) AS sgst

FROM outward_customer_order_mst a
JOIN outward_customer_order_det b ON a.id = b.mst_id
JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
JOIN order_mst d ON a.order_id = d.id
JOIN outlet e ON d.customer_id = e.id

WHERE d.order_type = 'outlet'
AND a.invoice_date BETWEEN ? AND ?

GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name, c.gst,a.status

UNION ALL

SELECT
a.status,
    a.order_id AS invoice_no,
    a.order_id as id,
    a.delivery_date AS invoice_date,
    c.name,
    'Advance Order' AS order_type,
        '0.00' AS delivery_charges,
    'sales' AS invoice_type,
    0 AS gst,

    -- ✅ ADVANCE: ALREADY EXCLUSIVE
    ROUND(SUM(b.total_price), 2) AS sub_total,

    SUM(b.mrp) AS total_mrp,
    0 AS cess_amt,

    ROUND(SUM(
        IF(b.gst_type = 'Outer GST',
            (b.outlet_price) -
            ((b.outlet_price) / (1 + (b.gst / 100))),
        0)
    ), 2) AS igst,

    ROUND(SUM(
        IF(b.gst_type = 'Inner GST',
            (
                (b.outlet_price) -
                ((b.outlet_price) / (1 + (b.gst / 100)))
            ) / 2,
        0)
    ), 2) AS cgst,

    ROUND(SUM(
        IF(b.gst_type = 'Inner GST',
            (
                (b.outlet_price) -
                ((b.outlet_price) / (1 + (b.gst / 100)))
            ) / 2,
        0)
    ), 2) AS sgst

FROM adv_order_mst a
JOIN adv_order_det b ON a.id = b.mst_id
JOIN customers c ON a.outlet_id = c.id

WHERE a.customer_type = 'customer'
AND (a.status != 'pending' AND a.status != 'processing')
AND a.delivery_date BETWEEN ? AND ?

GROUP BY a.delivery_date, a.id, c.name,a.order_id,a.status

UNION ALL

SELECT
a.status,
    a.order_id AS invoice_no,
    a.order_id as id,
    a.delivery_date AS invoice_date,
    c.outlet_name AS name,
    'Advance Order' AS order_type,
        '0.00' AS delivery_charges,
    'sales' AS invoice_type,
    0 AS gst,

    ROUND(SUM(b.total_price), 2) AS sub_total,

    SUM(b.mrp) AS total_mrp,
    0 AS cess_amt,

    ROUND(SUM(
        IF(b.gst_type = 'Outer GST',
            (b.outlet_price) -
            ((b.outlet_price) / (1 + (b.gst / 100))),
        0)
    ), 2) AS igst,

    ROUND(SUM(
        IF(b.gst_type = 'Inner GST',
            (
                (b.outlet_price) -
                ((b.outlet_price) / (1 + (b.gst / 100)))
            ) / 2,
        0)
    ), 2) AS cgst,

    ROUND(SUM(
        IF(b.gst_type = 'Inner GST',
            (
                (b.outlet_price) -
                ((b.outlet_price) / (1 + (b.gst / 100)))
            ) / 2,
        0)
    ), 2) AS sgst

FROM adv_order_mst a
JOIN adv_order_det b ON a.id = b.mst_id
JOIN outlet c ON a.outlet_id = c.id

WHERE a.customer_type = 'outlet'
AND (a.status != 'pending' AND a.status != 'processing')
AND a.delivery_date BETWEEN ? AND ?

GROUP BY a.delivery_date, a.id, c.outlet_name,a.order_id,a.status
";

        $params = [$fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt];

        $data = DB::select(
            $sql . " ORDER BY invoice_date",
            array_merge($params)
        );




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
                DB::raw("a.delivery_charges"),
                DB::raw("'Regular Order' as order_type"),
                DB::raw("'purchase' as invoice_type"),
                DB::raw("'complete' as status"),


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


    public function tallyReport(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->toDateString();
        $toDt   = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page   = $request->input("page", 1);
        $customer_type   = $request->input("customer_type");

        $limit  = 1000;
        $offset = ($page - 1) * $limit;

        // ✅ STEP 1: GST RATES
        $gstRatesRaw = DB::table('gst')->orderBy("gst", "asc")->pluck('gst')->toArray();
        $gstRates = array_map(fn($gst) => (int)$gst, $gstRatesRaw);

        // ✅ STEP 2: DYNAMIC COLUMNS
        $columnsRegular = [];
        $columnsAdvance = [];



        foreach ($gstRates as $gst) {

            /* ================= REGULAR ================= */

            // TOTAL (inclusive)
            $columnsRegular[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(c.gst) = $gst 
                THEN (b.qty * c.price * (100 - c.discount)) / 100
                ELSE 0 
            END
        ), 2
    ) AS total_$gst
    ";

            // TAXABLE
            $columnsRegular[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(c.gst) = $gst 
                THEN ((b.qty * c.price * (100 - c.discount)) / 100) / (1 + ($gst / 100))
                ELSE 0 
            END
        ), 2
    ) AS taxable_$gst
    ";

            // GST
            $columnsRegular[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(c.gst) = $gst 
                THEN ((b.qty * c.price * (100 - c.discount)) / 100)
                     - (((b.qty * c.price * (100 - c.discount)) / 100) / (1 + ($gst / 100)))
                ELSE 0 
            END
        ), 2
    ) AS gst_$gst
    ";


            /* ================= ADVANCE ================= */

            // TOTAL
            $columnsAdvance[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN b.outlet_price
                ELSE 0 
            END
        ), 2
    ) AS total_$gst
    ";

            // TAXABLE
            $columnsAdvance[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN b.outlet_price / (1 + ($gst / 100))
                ELSE 0 
            END
        ), 2
    ) AS taxable_$gst
    ";

            // GST
            $columnsAdvance[] = "
    ROUND(
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN b.outlet_price - (b.outlet_price / (1 + ($gst / 100)))
                ELSE 0 
            END
        ), 2
    ) AS gst_$gst
    ";
        }

        $dynamicColumnsRegular = implode(",\n", $columnsRegular);
        $dynamicColumnsAdvance = implode(",\n", $columnsAdvance);





        $sql = "
SELECT * FROM (

    -- ================= CUSTOMER REGULAR =================
    SELECT
        a.invoice_no,
        a.order_no AS id,
        a.invoice_date,
        e.name,
        a.is_invoice,
        a.status,
        'Regular Order' AS order_type,
        'customer' AS customer_type,
        c.gst_type,

        $dynamicColumnsRegular,

        SUM(
            ROUND(
                ((b.qty * c.price) * (1 - c.discount / 100)) 
                - (
                    ((b.qty * c.price) * (1 - c.discount / 100)) 
                    / (1 + (c.gst / 100))
                ), 2)
        ) AS total_gst,

        SUM((b.qty * c.price) * (1 - c.discount / 100)) AS total_amount

    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN customers e ON d.customer_id = e.id

    WHERE d.order_type = 'customer'
    
    AND a.invoice_date BETWEEN ? AND ?

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name, a.is_invoice,a.status,c.gst_type


    UNION ALL

    -- ================= OUTLET REGULAR =================
    SELECT
        a.invoice_no,
        a.order_no AS id,
        a.invoice_date,
        e.outlet_name AS name,
        a.is_invoice,
        a.status,
        'Regular Order' AS order_type,
        'outlet' AS customer_type,
        c.gst_type,

        $dynamicColumnsRegular,

        SUM(
            ROUND(
                ((b.qty * c.price) * (1 - c.discount / 100)) 
                - (
                    ((b.qty * c.price) * (1 - c.discount / 100)) 
                    / (1 + (c.gst / 100))
                ), 2)
        ) AS total_gst,

        SUM((b.qty * c.price) * (1 - c.discount / 100)) AS total_amount

    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN outlet e ON d.customer_id = e.id

    WHERE d.order_type = 'outlet'
    
    AND a.invoice_date BETWEEN ? AND ?

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name, a.is_invoice,a.status,c.gst_type


    UNION ALL

    -- ================= CUSTOMER ADVANCE =================
    SELECT
        a.order_id AS invoice_no,
        a.order_id AS id,
        a.delivery_date AS invoice_date,
        c.name,
        a.is_invoice,
        a.status,
        'Advance Order' AS order_type,
        'customer' AS customer_type,
        b.gst_type,

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id

    WHERE a.customer_type = 'customer'
      AND (a.status!='pending' and a.status!='processing') 
      AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.name, a.order_id, a.is_invoice,a.status,b.gst_type


    UNION ALL

    -- ================= OUTLET ADVANCE =================
    SELECT
        a.order_id AS invoice_no,
        a.order_id AS id,
        a.delivery_date AS invoice_date,
        c.outlet_name AS name,
        a.is_invoice,
        a.status,
        'Advance Order' AS order_type,
        'outlet' AS customer_type,
        b.gst_type,

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id

    WHERE a.customer_type = 'outlet'
    AND (a.status!='pending' and a.status!='processing') 
      AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.outlet_name, a.order_id, a.is_invoice,a.status,b.gst_type

) AS final_data

WHERE 1 = 1
";


        $params = [
            $fromDt,
            $toDt,
            $fromDt,
            $toDt,
            $fromDt,
            $toDt,
            $fromDt,
            $toDt
        ];

        // ✅ Apply customer_type filter safely
        if (!empty($customer_type)) {
            $sql .= " AND customer_type = ?";
            $params[] = $customer_type;
        }

        // ✅ Order + Pagination
        $sql .= " 
ORDER BY 
    CAST(SUBSTRING_INDEX(id,'-',-1) AS UNSIGNED) ASC
LIMIT ? OFFSET ?
";

        $params[] = $limit;
        $params[] = $offset;


        $sales = DB::select($sql, $params);


        $columnsPurchase = [];
        foreach ($gstRates as $gst) {

            // ✅ TAXABLE (no change)
            $columnsPurchase[] = "
        SUM(
            CASE 
                WHEN ROUND(b.gst) = $gst 
                THEN (b.qty * b.price)
                ELSE 0 
            END
        ) AS taxable_$gst
    ";

            // ✅ GST (ROUND AFTER SUM)
            $columnsPurchase[] = "
        ROUND(
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN (b.qty * b.price * $gst) / 100
                    ELSE 0 
                END
            ), 2
        ) AS gst_$gst
    ";

            // ✅ CESS (ROUND AFTER SUM)
            $columnsPurchase[] = "
        ROUND(
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN (b.qty * b.price * b.cess_tax) / 100
                    ELSE 0 
                END
            ), 2
        ) AS cess_$gst
    ";

            // ✅ TOTAL (ROUND AFTER SUM)
            $columnsPurchase[] = "
        ROUND(
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN 
                        (b.qty * b.price) 
                        + ((b.qty * b.price * $gst) / 100)
                        + ((b.qty * b.price * b.cess_tax) / 100)
                    ELSE 0 
                END
            ), 2
        ) AS total_$gst
    ";
        }



        $dynamicColumnsPurchase = implode(",\n", $columnsPurchase);

        $purchase = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->selectRaw("
        a.invoice_id,
        a.received_material_date as invoice_date,
        c.name as vendor,
        a.delivery_charges,
        'IGST' AS gst_type,
     
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
            ->groupBy("a.id", "a.invoice_id", "a.invoice_date", "c.name", "a.delivery_charges", "a.received_material_date")
            ->get();

        $tallyData = [];

        /* ================= SALES CONVERT ================= */
        foreach ($sales as $row) {

            $total = $row->total_amount;
            $total_gst = $row->total_gst;

            // ❌ Cancel invoice handling
            if ($row->status == 'cancel') {
                $total = 0;
            }

            /* -------- MAIN ROW -------- */
            $tallyData[] = (object)[
                "invoice_type" => "Sales",
                "date" => $row->invoice_date,
                "invoice_no" => $row->id,
                "ledger" => $row->name,
                "ledger_group" => "Sundry Debtors",
                "amount" => number_format($total, 2, '.', ''),
                "invoice_amount" => round($total),
                "gst_type" => $row->gst_type
            ];

            /* -------- GST LOOP -------- */
            foreach ($gstRates as $gst) {

                $taxable = $row->{"taxable_$gst"} ?? 0;
                $gstAmt  = $row->{"gst_$gst"} ?? 0;

                if ($taxable > 0) {

                    // SALES LEDGER
                    $tallyData[] = (object)[
                        "invoice_type" => "Sales",
                        "date" => $row->invoice_date,
                        "invoice_no" => $row->id,
                        "ledger" => "Sales @ $gst%",
                        "ledger_group" => "Sales Accounts",
                        "hsn" => "",
                        "gst" => $gst,
                        "amount" => number_format(-1 * $taxable, 2, '.', ''),
                        "gst_type" => $row->gst_type
                    ];

                    if ($row->gst_type == 'Outer GST') {
                        $tallyData[] = (object)[
                            "invoice_type" => "Sales",
                            "date" => $row->invoice_date,
                            "invoice_no" => $row->id,
                            "ledger" => "OUTPUT IGST $gst%",
                            "ledger_group" => "Duties & Taxes",
                            "amount" => number_format(-1 * $gstAmt, 2, '.', ''),
                            "gst_type" => $row->gst_type
                        ];
                    } else {

                        $cgst = round($gstAmt / 2, 2);
                        $sgst = round($gstAmt - $cgst, 2);

                        $tallyData[] = (object)[
                            "invoice_type" => "Sales",
                            "date" => $row->invoice_date,
                            "invoice_no" => $row->id,
                            "ledger" => "INPUT CGST " . ($gst / 2) . "%",
                            "ledger_group" => "Duties & Taxes",
                            "gst" => $gst / 2,
                            "amount" => number_format(- ($cgst), 2, '.', ''),
                        ];

                        $tallyData[] = (object)[
                            "invoice_type" => "Sales",
                            "date" => $row->invoice_date,
                            "invoice_no" => $row->id,
                            "ledger" => "INPUT SGST " . ($gst / 2) . "%",
                            "ledger_group" => "Duties & Taxes",
                            "gst" => $gst / 2,
                            "amount" => number_format(- ($sgst), 2, '.', ''),
                        ];
                    }
                }
            }
        }


        /* ================= PURCHASE CONVERT ================= */
        foreach ($purchase as $row) {

            $total = $row->grand_total + $row->delivery_charges;

            /* -------- MAIN ROW -------- */
            $tallyData[] = (object)[
                "invoice_type" => "Purchase",
                "date" => $row->invoice_date,
                "invoice_no" => $row->invoice_id,
                "ledger" => $row->vendor,
                "ledger_group" => "Sundry Creditors",
                "amount" => number_format($total, 2, '.', ''),
                "invoice_amount" => round($total),
                "gst_type" => $row->gst_type
            ];

            /* -------- GST LOOP -------- */
            foreach ($gstRates as $gst) {

                $taxable = $row->{"taxable_$gst"} ?? 0;
                $gstAmt  = $row->{"gst_$gst"} ?? 0;
                $cess    = $row->{"cess_$gst"} ?? 0;

                if ($taxable == 0 && $gstAmt == 0 && $cess == 0) continue;

                // NET PURCHASE
                if ($taxable > 0) {
                    $tallyData[] = (object)[
                        "invoice_type" => "Purchase",
                        "date" => $row->invoice_date,
                        "invoice_no" => $row->invoice_id,
                        "ledger" => "NET PURCHASE @ $gst%",
                        "ledger_group" => "Purchase Accounts",
                        "gst" => $gst,
                        "amount" => number_format(-$taxable, 2, '.', ''),
                    ];
                }

                // GST SPLIT
                if ($gstAmt > 0) {

                    // if ($row->gst_type == 'Outer GST') {

                    // IGST
                    $tallyData[] = (object)[
                        "invoice_type" => "Purchase",
                        "date" => $row->invoice_date,
                        "invoice_no" => $row->invoice_id,
                        "ledger" => "INPUT IGST $gst%",
                        "ledger_group" => "Duties & Taxes",
                        "gst" => $gst,
                        "amount" => number_format(-$gstAmt, 2, '.', ''),
                    ];
                    // } 


                    // else {

                    //     // CGST
                    //     $tallyData[] = (object)[
                    //         "invoice_type" => "Purchase",
                    //         "date" => $row->invoice_date,
                    //         "invoice_no" => $row->invoice_id,
                    //         "ledger" => "INPUT CGST " . ($gst / 2) . "%",
                    //         "ledger_group" => "Duties & Taxes",
                    //         "gst" => $gst / 2,
                    //         "amount" => number_format(- ($gstAmt / 2), 2, '.', ''),
                    //     ];

                    //     // SGST
                    //     $tallyData[] = (object)[
                    //         "invoice_type" => "Purchase",
                    //         "date" => $row->invoice_date,
                    //         "invoice_no" => $row->invoice_id,
                    //         "ledger" => "INPUT SGST " . ($gst / 2) . "%",
                    //         "ledger_group" => "Duties & Taxes",
                    //         "gst" => $gst / 2,
                    //         "amount" => number_format(- ($gstAmt / 2), 2, '.', ''),
                    //     ];
                    // }
                }

                // CESS
                if ($cess > 0) {
                    $tallyData[] = (object)[
                        "invoice_type" => "Purchase",
                        "date" => $row->invoice_date,
                        "invoice_no" => $row->invoice_id,
                        "ledger" => "CESS",
                        "ledger_group" => "Duties & Taxes",
                        "amount" => number_format($cess, 2, '.', ''),
                    ];
                }
            }


            /* DELIVERY CHARGES */
            if ($row->delivery_charges > 0) {
                $tallyData[] = (object)[
                    "invoice_type" => "Purchase",
                    "date" => $row->invoice_date,
                    "invoice_no" => $row->invoice_id,
                    "ledger" => "Delivery Charges",
                    "ledger_group" => "Indirect Expenses",
                    "amount" => number_format(-1 * $row->delivery_charges, 2, '.', ''),
                    "gst_type" => $row->gst_type
                ];
            }
        }

        /* ================= FINAL ================= */
        $data = $tallyData;

        return view("tally-report", compact("data"));
        echo "<pre>";
        print_r($data);
        die;
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
            ->whereDate("a.created_at", ">=", $fromDt)
            ->whereDate("a.created_at", "<=", $toDt)
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
            ->whereDate("a.created_at", ">=", $fromDt)
            ->whereDate("a.created_at", "<=", $toDt)
            ->groupBy("v.id", "v.outlet_name", "a.return_date", "a.id");


        $data = $purchase_return->union($sale_return)->get();
        return view("debit-credit-note", compact("data"));

        dd($data);
    }
}
