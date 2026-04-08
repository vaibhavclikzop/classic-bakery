<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Reports extends Controller
{
    public function PurchaseVariationReport(Request $request)
    {
        $fromDt = $request->input('fromDt', date('Y-m-d'));

        /* ================= RM ================= */
        $rm = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->select(
                "a.id as mst_id",
                "a.received_material_date as invoice_date",
                "b.product_id",
                DB::raw("c.name COLLATE utf8mb4_unicode_ci as vendor"),
                DB::raw("d.name COLLATE utf8mb4_unicode_ci as product"),
                "b.price",
                "b.qty"
            )
            ->where("b.type", "raw material");

        /* ================= FG ================= */
        $fg = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("finish_products_mst as d", "b.product_id", "d.id")
            ->select(
                "a.id as mst_id",
                "a.received_material_date as invoice_date",
                "b.product_id",
                DB::raw("c.name COLLATE utf8mb4_unicode_ci as vendor"),
                DB::raw("d.name COLLATE utf8mb4_unicode_ci as product"),
                "b.price",
                "b.qty"
            )
            ->where("b.type", "finished product");

        /* ================= UNION ================= */
        $query = DB::table(DB::raw("({$rm->toSql()} UNION ALL {$fg->toSql()}) as x"))
            ->mergeBindings($rm)
            ->mergeBindings($fg);

        /* ================= FILTER ================= */
        if ($fromDt) {
            $query->whereIn("product_id", function ($q) use ($fromDt) {
                $q->select("b.product_id")
                    ->from("stock_inward_mst as a")
                    ->join("stock_inward_det as b", "a.id", "b.mst_id")
                    ->whereDate("a.received_material_date", $fromDt);
            });
        }

        /* ================= FETCH ================= */
        $records = $query
            ->orderBy("product_id")
            ->orderByDesc("invoice_date")
            ->orderByDesc("mst_id")
            ->get();

        /* ================= GROUP ================= */
        $grouped = $records->groupBy("product_id");

        /* ================= VARIATION LOGIC ================= */
        $latestFive = $grouped->map(function ($items) {

            $items = $items
                ->sortByDesc(fn($item) => $item->invoice_date . '-' . $item->mst_id)
                ->values();

            // Last 5 purchases
            $lastFive = $items->take(5)->map(function ($item) {
                $item->price = round($item->price, 2);
                return $item;
            });

            // Check variation
            $uniquePrices = $lastFive->pluck('price')->unique();

            // ❌ No variation → skip
            if ($uniquePrices->count() <= 1) {
                return collect();
            }

            // ✅ Variation exists → return all 5
            return $lastFive;
        });

 




        $variationReport = $latestFive->map(function ($items) {

            $items = $items
                ->sortByDesc(function ($item) {
                    return $item->invoice_date . '-' . $item->mst_id;
                })
                ->values();

            $result = collect();

            $seenPrices = [];

            foreach ($items as $index => $item) {

                // Always take first (latest)
                if ($index == 0) {
                    $result->push($item);
                    $seenPrices[] = $item->price;
                    continue;
                }

                // Only take if price not already seen
                if (!in_array($item->price, $seenPrices)) {
                    $result->push($item);
                    $seenPrices[] = $item->price;
                }
            }

            return $result;
        });



        $filter = $latestFive
            ->filter(fn($items) => $items->isNotEmpty()) // remove empty
            ->flatten(1)
            ->values();




        return view("purchase-variation-report", compact("filter"));
    }


    public function PurchaseRegisterReport(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();

        $filterRawMaterial = DB::table("stock_inward_mst as a")
            ->select(
                "a.invoice_id as invoice_id",
                "a.received_material_date",
                "c.name as vendor",


                DB::raw("SUM(b.qty * b.price) as taxable_amount"),


                DB::raw("SUM((b.qty * b.price * b.gst) / 100) as gst_amount"),


                DB::raw("SUM((b.qty * b.price * b.cess_tax) / 100) as cess_amount"),


                DB::raw("
            SUM(
                (b.qty * b.price) +
                ((b.qty * b.price * b.gst) / 100) +
                ((b.qty * b.price * b.cess_tax) / 100)
            ) as total_amount
        "),


                "a.delivery_charges",


                DB::raw("
            SUM(
                (b.qty * b.price) +
                ((b.qty * b.price * b.gst) / 100) +
                ((b.qty * b.price * b.cess_tax) / 100)
            ) + a.delivery_charges as grand_total
        ")
            )
            ->join("stock_inward_det as b", "a.id", "=", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "=", "c.id");


        if ($fromDt) {
            $filterRawMaterial->whereDate("a.received_material_date", ">=", $fromDt);
        }

        if ($toDt) {
            $filterRawMaterial->whereDate("a.received_material_date", "<=", $toDt);
        }
        $rawMaterial = $filterRawMaterial->groupBy("a.invoice_id", "a.received_material_date", "c.name", "a.delivery_charges")->get();



        $filterFinishGoods = DB::table("stock_inward_mst_finish_goods as a")
            ->select(
                "a.id as invoice_id",
                "a.received_material_date",
                "c.name as vendor",


                DB::raw("SUM(b.qty * b.price) as taxable_amount"),


                DB::raw("SUM((b.qty * b.price * b.gst) / 100) as gst_amount"),


                DB::raw("SUM((b.qty * b.price * b.cess_tax) / 100) as cess_amount"),


                DB::raw("
            SUM(
                (b.qty * b.price) +
                ((b.qty * b.price * b.gst) / 100) +
                ((b.qty * b.price * b.cess_tax) / 100)
            ) as total_amount
        "),


                "a.delivery_charges",


                DB::raw("
            SUM(
                (b.qty * b.price) +
                ((b.qty * b.price * b.gst) / 100) +
                ((b.qty * b.price * b.cess_tax) / 100)
            ) + a.delivery_charges as grand_total
        ")
            )
            ->join("stock_inward_det_finish_goods as b", "a.id", "=", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "=", "c.id");

        if ($fromDt) {
            $filterFinishGoods->whereDate("a.received_material_date", ">=", $fromDt);
        }

        if ($toDt) {
            $filterFinishGoods->whereDate("a.received_material_date", "<=", $toDt);
        }

        $finishGoods = $filterFinishGoods->groupBy("a.id", "a.received_material_date", "c.name", "a.delivery_charges")->get();
        $data = $rawMaterial->merge($finishGoods);
        $data = $data->sortBy('received_material_date')->values();

        return view("purchase-register-report", compact("data"));
    }



    public function SaleRegisterReport(Request $request)
    {
        return view("sale-register-report");
    }


    public function getSaleRegisterReportData(Request $request)
    {

        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();

        $page = $request->input("page", 1);
        $limit = 1000;
        $offset = ($page - 1) * $limit;

        $customerType = request('customer_type');

        $sqlParts = [];

        /* ================= CUSTOMER ================= */
        if (!$customerType || $customerType == 'customer') {

            $sqlParts[] = "
    SELECT
        a.invoice_no,
        a.order_no as id,
        a.invoice_date,
        e.name AS name,
        a.is_invoice,
        a.status,
        'Regular Order' AS order_type,

        SUM((b.qty * c.price) * (1 - c.discount / 100)) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,

        ROUND(SUM(
            IF(c.gst_type = 'Outer GST',
                ((b.qty * c.price) * (1 - c.discount / 100)) -
                (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100))),
            0)
        ), 2) AS igst,

        ROUND(SUM(
            IF(c.gst_type = 'Inner GST',
                (
                    ((b.qty * c.price) * (1 - c.discount / 100)) -
                    (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100)))
                ) / 2,
            0)
        ), 2) AS cgst,

        ROUND(SUM(
            IF(c.gst_type = 'Inner GST',
                (
                    ((b.qty * c.price) * (1 - c.discount / 100)) -
                    (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100)))
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

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name, a.is_invoice,a.status
    ";

            $sqlParts[] = "
    SELECT
        a.order_id AS invoice_no,
        a.order_id as id,
        a.delivery_date AS invoice_date,
        c.name,
        a.is_invoice,
        a.status,
        'Advance Order' AS order_type,

        SUM(b.total_price) AS sub_total,
        SUM(b.mrp*b.weight) AS total_mrp,
        0 AS cess_amt,

        ROUND(SUM(
            IF(b.gst_type = 'Outer GST',
                (b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100))),
            0)
        ), 2) AS igst,

        ROUND(SUM(
            IF(b.gst_type = 'Inner GST',
                ((b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100)))) / 2,
            0)
        ), 2) AS cgst,

        ROUND(SUM(
            IF(b.gst_type = 'Inner GST',
                ((b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100)))) / 2,
            0)
        ), 2) AS sgst

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id

    WHERE a.customer_type = 'customer'
      AND (a.status!='pending' or a.status!='processing')
    AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.name, a.order_id, a.is_invoice,a.status
    ";
        }

        /* ================= OUTLET ================= */
        if (!$customerType || $customerType == 'outlet') {

            $sqlParts[] = "
    SELECT
        a.invoice_no,
        a.order_no AS id,
        a.invoice_date,
        e.outlet_name AS name,
        a.is_invoice,
        a.status,
        'Regular Order' AS order_type,

        SUM((b.qty * c.price) * (1 - c.discount / 100)) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,

        ROUND(SUM(
            IF(c.gst_type = 'Outer GST',
                ((b.qty * c.price) * (1 - c.discount / 100)) -
                (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100))),
            0)
        ), 2) AS igst,

        ROUND(SUM(
            IF(c.gst_type = 'Inner GST',
                (
                    ((b.qty * c.price) * (1 - c.discount / 100)) -
                    (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100)))
                ) / 2,
            0)
        ), 2) AS cgst,

        ROUND(SUM(
            IF(c.gst_type = 'Inner GST',
                (
                    ((b.qty * c.price) * (1 - c.discount / 100)) -
                    (((b.qty * c.price) * (1 - c.discount / 100)) / (1 + (c.gst / 100)))
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

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name, a.is_invoice,a.status
    ";

            $sqlParts[] = "
    SELECT
        a.order_id AS invoice_no,
        a.order_id as id,
        a.delivery_date AS invoice_date,
        c.outlet_name AS name,
        a.is_invoice,
        a.status,
        'Advance Order' AS order_type,

        SUM(b.total_price) AS sub_total,
  SUM(b.mrp*b.weight) AS total_mrp,
        0 AS cess_amt,

        ROUND(SUM(
            IF(b.gst_type = 'Outer GST',
                (b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100))),
            0)
        ), 2) AS igst,

        ROUND(SUM(
            IF(b.gst_type = 'Inner GST',
                ((b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100)))) / 2,
            0)
        ), 2) AS cgst,

        ROUND(SUM(
            IF(b.gst_type = 'Inner GST',
                ((b.outlet_price) - ((b.outlet_price)/(1+(b.gst/100)))) / 2,
            0)
        ), 2) AS sgst

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id

    WHERE a.customer_type = 'outlet'
  AND (a.status!='pending' and a.status!='processing') 
    AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.outlet_name, a.order_id, a.is_invoice,a.status
    ";
        }

        /* ================= FINAL ================= */

        $sql = implode(" UNION ALL ", $sqlParts);

        $params = [];
        foreach ($sqlParts as $part) {
            $params[] = $fromDt;
            $params[] = $toDt;
        }

        $data = DB::select(
            $sql . " ORDER BY invoice_date LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        return response()->json([
            'data' => $data
        ]);
    }


    public function CategorySubCategoryReport(Request $request)
    {

        $fromDt = request("fromDt");
        $toDt = request("toDt");
        $f_category_id = request("f_category_id");
        $f_sub_category_id = request("f_sub_category_id");



        $returnData = DB::table('sale_return_det as sr')
            ->join('sale_return_mst as srm', 'srm.id', '=', 'sr.mst_id')
            ->where('srm.status', 'complete')
            ->select('sr.product_id', DB::raw('SUM(sr.qty) as return_qty'))
            ->groupBy('sr.product_id')
            ->pluck('return_qty', 'product_id');



        $mst = DB::table("order_mst as a")
            ->select(
                "c.id as product_id",
                "c.name as product_name",
                "d.name as category",
                "e.name as sub_category",
                DB::raw("SUM(b.qty) as qty"),
                DB::raw("SUM(b.price * b.qty) as price")
            )
            ->join("order_det as b", "a.id", "b.mst_id")
            ->join("finish_products_mst as c", "b.product_id", "c.id")
            ->join("f_product_category as d", "c.f_category_id", "d.id")
            ->join("f_product_sub_category as e", "c.f_sub_category_id", "e.id")
            ->whereNotIn("a.status", ["pending", "cancel", "processing"]);
        if ($fromDt) {
            $mst->whereDate("a.delivery_date", ">=", $fromDt);
        }
        if ($toDt) {
            $mst->whereDate("a.delivery_date", "<=", $toDt);
        }
        if ($f_category_id) {
            $mst->where("c.f_category_id", "=", $f_category_id);
        }
        if ($f_sub_category_id) {
            $mst->where("c.f_sub_category_id", "=", $f_sub_category_id);
        }

        $data = $mst->groupBy("c.id", "c.name", "d.name", "e.name")
            ->get();


        foreach ($data as $item) {
            $item->return_qty = $returnData[$item->product_id] ?? 0;
            $item->final_qty = $item->qty - $item->return_qty;
        }

        $sub_category = collect();
        $category = DB::table("f_product_category")->get();
        if ($f_category_id) {
            $sub_category = DB::table("f_product_sub_category")->where("f_category_id", $f_category_id)->get();
        }

        return view("category-subcategory-report", compact("data", "category", "sub_category"));
    }

    public function CustomerWiseReport(Request $request)
    {


        $date = request("date", date("Y-m-d"));



        $product_id = [];
        $order_id = [];


        $mst = DB::table("work_order_mst as a")
            ->select("b.product_id", "b.order_id")
            ->join("work_order_det as b", "a.id", "b.mst_id");
        if ($date) {
            $mst->whereDate("a.delivery_date", $date);
        }


        $work_order_mst = $mst->get();

        foreach ($work_order_mst as $value) {
            $product_id[] = $value->product_id;
            $order_id[] = $value->order_id;
        }

        $product_id = array_unique($product_id);
        sort($product_id);

        $order_id = array_unique($order_id);
        sort($order_id);

        $report = [];

        foreach ($product_id as $product) {
            $f_product = DB::table("finish_products_mst as a")
                ->select("a.*", "b.name as sub_category")
                ->join("f_product_sub_category as b", "a.f_sub_category_id", "b.id")
                ->where("a.id", $product)
                ->first();

            $emp_data = [['name' => $f_product->name, "sub_category" => $f_product->sub_category]];

            foreach ($order_id as $order) {
                $customer = DB::table("order_mst as a")
                    ->select("b.nickname as customer", "b.id as customer_id", "c.qty")
                    ->join("customers as b", "a.customer_id", "b.id")
                    ->join("work_order_det as c", "a.id", "c.order_id")
                    ->where("a.id", $order)
                    ->where("c.product_id", $product)
                    ->where("a.order_type", "customer")
                    ->first();

                $outlet = DB::table("order_mst as a")
                    ->select("b.nickname as customer", "b.id as customer_id", "c.qty")
                    ->join("outlet as b", "a.customer_id", "b.id")
                    ->join("work_order_det as c", "a.id", "c.order_id")
                    ->where("a.id", $order)
                    ->where("c.product_id", $product)
                    ->where("a.order_type", "outlet")
                    ->first();

                $details = $customer ?? $outlet;

                // Ensure Lucky Outlet shows 0 if no quantity is found
                $emp_data[] = [
                    'order_id' => $order,
                    'customer' => $details->customer ?? '',
                    'qty' => $details->qty ?? 0,  // Default to 0 if no quantity
                    'product_id' => $product,
                    'customer_id' => $details->customer_id ?? 0,
                ];
            }

            $report[] = $emp_data;
        }

        // Ensure unique customer listing and handling missing quantities
        $customers = collect();
        foreach ($order_id as $order) {
            $customer = DB::table("order_mst as a")
                ->select("b.nickname as customer")
                ->join("customers as b", "a.customer_id", "b.id")
                ->where("a.id", $order)
                ->where("a.order_type", "customer")
                ->first();

            $outlet = DB::table("order_mst as a")
                ->select("b.nickname as customer")
                ->join("outlet as b", "a.customer_id", "b.id")
                ->where("a.id", $order)
                ->where("a.order_type", "outlet")
                ->first();

            $customers[] = $customer ?? $outlet;
        }
        // echo "<pre>";
        // print_r($report);
        // die;

        return view("customer-wise-report", compact("report", "customers"));
    }

    public function saleReportTaxBifurcation(Request $request)
    {
        $gstRates = DB::table('gst')->orderBy("gst", "asc")->get();
        return view("report.sale-report-tax-bifurcation", compact("gstRates"));
    }

























    public function getSaleReportGstBifurcation(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
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

            // ================= REGULAR (GST INCLUDED) =================

            // TOTAL (inclusive)
            $columnsRegular[] = "
            SUM(
                CASE 
                    WHEN ROUND(c.gst) = $gst 
                    THEN (b.qty * c.price) * (1 - c.discount / 100)
                    ELSE 0 
                END
            ) AS total_$gst
        ";

            // TAXABLE (extract from inclusive)
            $columnsRegular[] = "
            SUM(
                CASE 
                    WHEN ROUND(c.gst) = $gst 
                    THEN ROUND(
                        ((b.qty * c.price) * (1 - c.discount / 100)) 
                        / (1 + ($gst / 100)), 2
                    )
                    ELSE 0 
                END
            ) AS taxable_$gst
        ";

            // GST
            $columnsRegular[] = "
            SUM(
                CASE 
                    WHEN ROUND(c.gst) = $gst 
                    THEN ROUND(
                        ((b.qty * c.price) * (1 - c.discount / 100)) 
                        - (
                            ((b.qty * c.price) * (1 - c.discount / 100)) 
                            / (1 + ($gst / 100))
                        ), 2
                    )
                    ELSE 0 
                END
            ) AS gst_$gst
        ";


            // ================= ADVANCE =================

            // TOTAL (already inclusive)
            $columnsAdvance[] = "
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN b.outlet_price
                    ELSE 0 
                END
            ) AS total_$gst
        ";

            // TAXABLE
            $columnsAdvance[] = "
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN ROUND(
                        b.outlet_price / (1 + ($gst / 100)), 2
                    )
                    ELSE 0 
                END
            ) AS taxable_$gst
        ";

            // GST
            $columnsAdvance[] = "
            SUM(
                CASE 
                    WHEN ROUND(b.gst) = $gst 
                    THEN ROUND(
                        b.outlet_price - (b.outlet_price / (1 + ($gst / 100)))
                    , 2)
                    ELSE 0 
                END
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

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name, a.is_invoice,a.status


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

    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name, a.is_invoice,a.status


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

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id

    WHERE a.customer_type = 'customer'
      AND (a.status!='pending' and a.status!='processing') 
      AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.name, a.order_id, a.is_invoice,a.status


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

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id

    WHERE a.customer_type = 'outlet'
    AND (a.status!='pending' and a.status!='processing') 
      AND a.delivery_date BETWEEN ? AND ?

    GROUP BY a.delivery_date, a.id, c.outlet_name, a.order_id, a.is_invoice,a.status

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


        $data = DB::select($sql, $params);


        return response()->json([
            'data' => $data
        ]);
    }










    public function getSaleReportGstBifurcationOld(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt   = $request->input("toDt") ?: Carbon::now()->toDateString();
        $page   = $request->input("page", 1);

        $limit  = 1000;
        $offset = ($page - 1) * $limit;






        // ✅ STEP 1: GET GST RATES
        $gstRatesRaw = DB::table('gst')->orderBy("gst", "asc")->pluck('gst')->toArray();

        $gstRates = array_map(function ($gst) {
            return (int)$gst; // 5.00 → 5
        }, $gstRatesRaw);


        // ✅ STEP 2: BUILD DYNAMIC COLUMNS (SAFE WAY)
        $columnsRegular = [];
        $columnsAdvance = [];

        foreach ($gstRates as $gst) {

            // TAXABLE AMOUNT (after discount)
            $columnsRegular[] = "
        SUM(
            CASE 
                WHEN ROUND(c.gst) = $gst 
                THEN (b.qty * c.price) * (1 - c.discount / 100)
                ELSE 0 
            END
        ) AS taxable_$gst
    ";

            // GST AMOUNT (row-level rounded for accuracy)
            $columnsRegular[] = "
        SUM(
            CASE 
                WHEN ROUND(c.gst) = $gst 
                THEN ROUND(
                    ((b.qty * c.price) * (1 - c.discount / 100)) 
                    - (
                        ((b.qty * c.price) * (1 - c.discount / 100)) 
                        / (1 + ($gst / 100))
                    ), 2)
                ELSE 0 
            END
        ) AS gst_$gst
    ";

            // ADVANCE (b.gst)
            $columnsAdvance[] = "SUM(CASE WHEN ROUND(b.gst) = $gst THEN b.outlet_price ELSE 0 END) AS taxable_$gst";

            $columnsAdvance[] = "SUM(CASE WHEN ROUND(b.gst) = $gst THEN 
        b.outlet_price - (b.outlet_price/(1+($gst/100)))
    ELSE 0 END) AS gst_$gst";
        }


        $dynamicColumnsRegular = implode(",\n", $columnsRegular);
        $dynamicColumnsAdvance = implode(",\n", $columnsAdvance);


        // ✅ STEP 3: FINAL QUERY
        $sql = "
SELECT * FROM (

    -- CUSTOMER REGULAR
SELECT
    a.invoice_no,
    a.order_no AS id,
    a.invoice_date,
    e.name,
    a.is_invoice,
    'Regular Order' AS order_type,

    $dynamicColumnsRegular,

    -- TOTAL GST (row-level rounding)
    SUM(
        ROUND(
            ((b.qty * c.price) * (1 - c.discount / 100)) 
            - (
                ((b.qty * c.price) * (1 - c.discount / 100)) 
                / (1 + (c.gst / 100))
            ), 2)
    ) AS total_gst,

    -- TOTAL AMOUNT (after discount)
    SUM((b.qty * c.price) * (1 - c.discount / 100)) AS total_amount

FROM outward_customer_order_mst a

JOIN outward_customer_order_det b 
    ON a.id = b.mst_id

JOIN order_det c 
    ON b.product_id = c.product_id 
    AND a.order_id = c.mst_id

JOIN order_mst d 
    ON a.order_id = d.id

JOIN customers e 
    ON d.customer_id = e.id

WHERE d.order_type = 'customer'
AND a.invoice_date BETWEEN ? AND ?

GROUP BY 
    a.invoice_no, 
    a.invoice_date, 
    a.order_no, 
    e.name, 
    a.is_invoice

    UNION ALL

    -- OUTLET REGULAR
SELECT
    a.invoice_no,
    a.order_no AS id,
    a.invoice_date,
    e.outlet_name AS name,
    a.is_invoice,
    'Regular Order' AS order_type,

    $dynamicColumnsRegular,

    -- TOTAL GST (row-level rounded sum)
    SUM(
        ROUND(
            ((b.qty * c.price) * (1 - c.discount / 100)) 
            - (
                ((b.qty * c.price) * (1 - c.discount / 100)) 
                / (1 + (c.gst / 100))
            ), 2)
    ) AS total_gst,

    -- TOTAL AMOUNT (after discount)
    SUM((b.qty * c.price) * (1 - c.discount / 100)) AS total_amount

FROM outward_customer_order_mst a

JOIN outward_customer_order_det b 
    ON a.id = b.mst_id

JOIN order_det c 
    ON b.product_id = c.product_id 
    AND a.order_id = c.mst_id

JOIN order_mst d 
    ON a.order_id = d.id

JOIN outlet e 
    ON d.customer_id = e.id

WHERE d.order_type = 'outlet'
AND a.invoice_date BETWEEN ? AND ?

GROUP BY 
    a.invoice_no, 
    a.invoice_date, 
    a.order_no, 
    e.outlet_name, 
    a.is_invoice

    UNION ALL

    -- CUSTOMER ADVANCE
    SELECT
        a.order_id AS invoice_no,
        a.order_id AS id,
        a.order_date AS invoice_date,
        c.name,
        a.is_invoice,
        'Advance Order',

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id

    WHERE a.customer_type = 'customer'
    
      AND a.order_date BETWEEN ? AND ?

    GROUP BY a.order_date, a.id, c.name, a.order_id, a.is_invoice

    UNION ALL

    -- OUTLET ADVANCE
    SELECT
        a.order_id AS invoice_no,
        a.order_id AS id,
        a.order_date AS invoice_date,
        c.outlet_name,
        a.is_invoice,
        'Advance Order',

        $dynamicColumnsAdvance,

        SUM(b.outlet_price - (b.outlet_price/(1+(b.gst/100)))) AS total_gst,
        SUM(b.outlet_price) AS total_amount

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id

    WHERE a.customer_type = 'outlet'
   
      AND a.order_date BETWEEN ? AND ?

    GROUP BY a.order_date, a.id, c.outlet_name, a.order_id, a.is_invoice

) AS final_data

ORDER BY invoice_date DESC
LIMIT ? OFFSET ?
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

        $data = DB::select($sql, array_merge($params, [$limit, $offset]));

        // echo "<pre>";
        // print_r($data);
        // die;

        return response()->json([
            'data' => $data
        ]);
    }

    public function departmentWiseTreadingReport(Request $request)
    {

        $date = request("date");


        // Step 1: Get ALL customers (Outlet + Customer)
        $customers = DB::table('order_mst as a')
            ->leftJoin('outlet as o', function ($join) {
                $join->on('a.customer_id', '=', 'o.id')
                    ->where('a.order_type', 'outlet');
            })
            ->leftJoin('customers as c', function ($join) {
                $join->on('a.customer_id', '=', 'c.id')
                    ->where('a.order_type', 'customer');
            })
            ->select(
                'a.customer_id',
                DB::raw("
            CASE 
                WHEN a.order_type = 'outlet' THEN CONCAT('', o.nickname)
                ELSE CONCAT(' ', c.name)
            END as outlet_name
        ")
            )
            ->whereDate("a.delivery_date", $date)
            ->whereIn('a.order_type', ['outlet', 'customer'])
            ->distinct()
            ->orderBy('outlet_name')
            ->get();


        // Step 2: Build dynamic columns
        $columns = [];

        foreach ($customers as $cust) {

            $colName = preg_replace('/[^A-Za-z0-9_]/', '_', $cust->outlet_name);

            $columns[] = "
        SUM(
            CASE 
                WHEN a.customer_id = {$cust->customer_id}
                THEN b.qty
                ELSE 0
            END
        ) AS `$colName`
    ";
        }

        $dynamicColumns = !empty($columns) ? implode(",\n", $columns) . "," : "";


        // Step 3: Final Query
        $query = "
        SELECT
            p.name AS product,

            $dynamicColumns

            SUM(b.qty) AS total_qty

        FROM order_det b

        JOIN order_mst a 
            ON b.mst_id = a.id

        JOIN finish_products_mst p 
            ON b.product_id = p.id

        WHERE 
            p.f_category_id = 2
            AND DATE(a.delivery_date) = ?
            AND a.order_type IN ('outlet', 'customer')

        GROUP BY 
            b.product_id, 
            p.name

        ORDER BY p.name
        ";


        // Step 4: Execute Query
        $data = DB::select($query, [$date]);



        // Step 5: Pass BOTH data + customers
        return view("report.department-wise-trading-report", compact("data", "customers"));
    }





    public function saleRegisterUserWise(Request $request)
    {
        $fromDate = request("fromDate");
        $toDate   = request("toDate");
        $customer_type   = request("customer_type");
        $user_id   = request("user_id");

        /* OUTLET */
        $outlet  = DB::table("outward_customer_order_mst as a")
            ->join("order_mst as c", "a.order_id", "c.id")
            ->join("outward_customer_order_det as od", "a.id", "od.mst_id")
            ->join("outlet", "c.customer_id", "outlet.id")
            ->join("users as u", "a.user_id", "u.id")
            ->join("order_det as o", function ($join) {
                $join->on("od.product_id", "=", "o.product_id")
                    ->on("a.order_id", "=", "o.mst_id");
            })
            ->where("c.order_type", "outlet")

            ->whereBetween("a.invoice_date", [$fromDate, $toDate])
            ->when($user_id, function ($q) use ($user_id) {
                $q->where("a.user_id", $user_id);
            })

            ->when($customer_type, function ($q) use ($customer_type) {
                $q->where("c.order_type", $customer_type);
            })

            ->groupBy("a.invoice_no", "a.invoice_date", "a.order_no", "outlet.outlet_name", "u.name", "a.created_at", "a.id", "a.status")

            ->select(
                "a.id",
                "a.status",
                "a.order_no",
                "a.invoice_date",
                "outlet.outlet_name as customer_name",
                "u.name as user",
                "a.created_at",
                DB::raw("sum(o.mrp*o.qty) as mrp")
            )

            ->selectRaw("
                    ROUND(SUM(
                        (od.qty * o.price) * (1 - o.discount / 100)
                    ), 2) as grand_total
                ");


        //     ->orderBy("a.invoice_date", "asc")
        //     ->get();
        // echo "<pre>";
        // print_r($outlet);

        // die;


        /* CUSTOMER */
        $customer = DB::table("outward_customer_order_mst as a")
            ->join("order_mst as c", "a.order_id", "c.id")
            ->join("outward_customer_order_det as od", "a.id", "od.mst_id")
            ->join("customers", "c.customer_id", "customers.id")
            ->join("users as u", "a.user_id", "u.id")
            ->join("order_det as o", function ($join) {
                $join->on("od.product_id", "=", "o.product_id")
                    ->on("a.order_id", "=", "o.mst_id");
            })
            ->where("c.order_type", "customer")

            ->whereBetween("a.invoice_date", [$fromDate, $toDate])
            ->when($user_id, function ($q) use ($user_id) {
                $q->where("a.user_id", $user_id);
            })

            ->when($customer_type, function ($q) use ($customer_type) {
                $q->where("c.order_type", $customer_type);
            })

            ->groupBy("a.invoice_no", "a.invoice_date", "a.order_no", "customers.name", "u.name", "a.created_at", "a.id", "a.status")

            ->select(
                "a.id",
                "a.status",
                "a.order_no",
                "a.invoice_date",
                "customers.name as customer_name",
                "u.name as user",
                "a.created_at",
                DB::raw("sum(o.mrp*o.qty) as mrp")
            )

            ->selectRaw("
                    ROUND(SUM(
                        (od.qty * o.price) * (1 - o.discount / 100)
                    ), 2) as grand_total
                ");

        /* ADV OUTLET */
        $advOutlet = DB::table("adv_order_mst as a")
            ->select(
                "a.id",
                "a.status",
                "a.order_id as order_no",
                "a.delivery_date as invoice_date",
                "c.outlet_name as user",
                "b.name as customer_name",
                "a.created_at",
                DB::raw("sum(d.mrp*d.weight) as mrp"),
                "d.total_price as grand_total"

            )
            ->join("users as b", "a.user_id", "b.id")
            ->join("outlet as c", "a.outlet_id", "c.id")
            ->join("adv_order_det as d", "a.id", "d.mst_id")
            ->where("a.customer_type", "outlet")
            ->whereNotIn("a.status", ["pending", "processing"])

            ->whereBetween("a.delivery_date", [$fromDate, $toDate])
            ->when($user_id, function ($q) use ($user_id) {
                $q->where("a.user_id", $user_id);
            })

            ->when($customer_type, function ($q) use ($customer_type) {
                $q->where("a.customer_type", $customer_type);
            })
            ->groupBy("a.order_id", "a.delivery_date", "b.name", "c.outlet_name", "a.created_at", "d.total_price", "a.id", "a.status", "d.id");

        /* ADV CUSTOMER */
        $advCustomer = DB::table("adv_order_mst as a")
            ->select(
                "a.id",
                "a.status",
                "a.order_id as order_no",
                "a.delivery_date as invoice_date",
                "c.name as user",
                "b.name as customer_name",
                "a.created_at",
                DB::raw("sum(d.mrp*d.qty) as mrp"),
                "d.total_price as grand_total",

            )
            ->join("users as b", "a.user_id", "b.id")
            ->join("customers as c", "a.outlet_id", "c.id")
            ->join("adv_order_det as d", "a.id", "d.mst_id")
            ->where("a.customer_type", "customer")
            ->whereNotIn("a.status", ["pending", "processing"])
            ->whereBetween("a.delivery_date", [$fromDate, $toDate])
            ->when($user_id, function ($q) use ($user_id) {
                $q->where("a.user_id", $user_id);
            })

            ->when($customer_type, function ($q) use ($customer_type) {
                $q->where("a.customer_type", $customer_type);
            })
            ->groupBy("a.order_id", "a.delivery_date", "b.name", "c.name", "a.created_at", "d.total_price", "a.id", "a.status", "d.id");

        /* FINAL UNION */
        $data = $outlet
            ->unionAll($customer)
            ->unionAll($advOutlet)
            ->unionAll($advCustomer)
            ->orderByRaw("
    CAST(SUBSTRING_INDEX(order_no, '-', -1) AS UNSIGNED) ASC
")
            ->get();

        $users = DB::table("users")->get();

        return view("report.sale-register-user-wise", compact("data", "users"));
    }
}
