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

        // Step 1️⃣: Base query
        $query = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->select(
                "a.id as mst_id",
                "a.invoice_date",
                "b.product_id",
                "c.name as vendor",
                "d.name as product",
                "b.price",
                "b.qty"
            );

        // Step 2️⃣: Filter only products that have at least one invoice on fromDt
        if ($fromDt) {
            $query->whereIn("b.product_id", function ($q) use ($fromDt) {
                $q->select("b.product_id")
                    ->from("stock_inward_mst as a")
                    ->join("stock_inward_det as b", "a.id", "b.mst_id")
                    ->whereDate("a.invoice_date", $fromDt);
            });
        }

        // Step 3️⃣: Fetch ordered data (all entries for those products)
        $records = $query
            ->orderBy("b.product_id")
            ->orderByDesc("a.invoice_date")
            ->orderByDesc("a.id")
            ->get();

        // Step 4️⃣: Group by product_id
        $grouped = $records->groupBy("product_id");

        // Step 5️⃣: For each product, take last 5 purchases
        $latestFive = $grouped->map(function ($items) {
            return $items
                ->sortByDesc(function ($item) {
                    return $item->invoice_date . '-' . $item->mst_id;
                })
                ->take(5);
        });

        // Step 6️⃣: Filter only those products where price varied
        $variationReport = $latestFive->filter(function ($entries) {
            $uniquePrices = $entries->pluck("price")->unique();
            return $uniquePrices->count() > 1;
        });

        // Step 7️⃣: Flatten for Blade display
        $filter = $variationReport->flatten(1)->values();





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
                DB::raw("SUM((b.qty * b.price) + ((b.qty * b.price * b.gst) / 100)+ ((b.qty * b.price * b.cess_tax) / 100)) as total_amount"),
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
        $rawMaterial = $filterRawMaterial->groupBy("a.invoice_id", "a.received_material_date", "c.name", "a.delivery_charges")->get();



        $filterFinishGoods = DB::table("stock_inward_mst_finish_goods as a")
            ->select(
                "a.id as invoice_id",
                "a.received_material_date",
                "c.name as vendor",
                DB::raw("SUM((b.qty * b.price) + ((b.qty * b.price * b.gst) / 100)+((b.qty * b.price * b.cess_tax) / 100)) as total_amount"),
                "a.delivery_charges"

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

        $sql = "
             SELECT
        a.invoice_no,
        a.order_no as id,
        a.invoice_date,
        e.name AS name,
        a.is_invoice,
        'Regular Order' AS order_type,
        SUM(b.qty * c.price) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,
        ROUND(SUM(IF(c.gst_type = 'Outer GST', (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/100))), 0)), 2) AS igst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST',  (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/200))), 0)), 2) AS cgst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST',  (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/200))), 0)), 2) AS sgst
    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN customers e ON d.customer_id = e.id
    WHERE d.order_type = 'customer'
      AND a.invoice_date BETWEEN ? AND ?
    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.name,a.is_invoice

    UNION ALL

    SELECT
        a.invoice_no,
        a.order_no as id,
        a.invoice_date,
        e.outlet_name AS name,
        a.is_invoice,
        'Regular Order' AS order_type,
        SUM(b.qty * c.price) AS sub_total,
        SUM(b.qty * c.mrp) AS total_mrp,
        SUM(c.cess_amt) AS cess_amt,
        ROUND(SUM(IF(c.gst_type = 'Outer GST', (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/100))), 0)), 2) AS igst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST',  (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/200))), 0)), 2) AS cgst,
        ROUND(SUM(IF(c.gst_type = 'Inner GST',  (b.qty * c.price) -((b.qty*c.price)/(1+(c.gst/200))), 0)), 2) AS sgst
    FROM outward_customer_order_mst a
    JOIN outward_customer_order_det b ON a.id = b.mst_id
    JOIN order_det c ON b.product_id = c.product_id AND a.order_id = c.mst_id
    JOIN order_mst d ON a.order_id = d.id
    JOIN outlet e ON d.customer_id = e.id
    WHERE d.order_type = 'outlet'
      AND a.invoice_date BETWEEN ? AND ?
    GROUP BY a.invoice_no, a.invoice_date, a.order_no, e.outlet_name,a.is_invoice

    UNION ALL

    SELECT
        a.order_id AS invoice_no,
        a.order_id as id,
        a.order_date AS invoice_date,
        c.name,
        a.is_invoice,
        'Advance Order' AS order_type,
        SUM(b.total_price) AS sub_total,
        SUM(b.mrp) AS total_mrp,
        0 AS cess_amt,
          ROUND(SUM(IF(b.gst_type = 'Outer GST', (b.outlet_price) -((b.outlet_price)/(1+(b.gst/100))), 0)), 2) AS igst,
        ROUND(SUM(IF(b.gst_type = 'Inner GST',  (b.outlet_price) -((b.outlet_price)/(1+(b.gst/200))), 0)), 2) AS cgst,
        ROUND(SUM(IF(b.gst_type = 'Inner GST',  (b.outlet_price) -((b.outlet_price)/(1+(b.gst/200))), 0)), 2) AS sgst
    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN customers c ON a.outlet_id = c.id
    WHERE a.customer_type = 'customer' 
     AND (a.status = 'dispatch' OR a.status = 'delivered' or a.is_invoice=1)
      AND a.order_date BETWEEN ? AND ?
    GROUP BY a.order_date, a.id, c.name,a.order_id,a.is_invoice

    UNION ALL

    SELECT
        a.order_id AS invoice_no,
        a.order_id as id,
        a.order_date AS invoice_date,
        c.outlet_name AS name,
        a.is_invoice,
        'Advance Order' AS order_type,
        SUM(b.total_price) AS sub_total,
        SUM(b.mrp) AS total_mrp,
        0 AS cess_amt,
         ROUND(SUM(IF(b.gst_type = 'Outer GST', (b.outlet_price) -((b.outlet_price)/(1+(b.gst/100))), 0)), 2) AS igst,
      ROUND(SUM(IF(b.gst_type = 'Inner GST', ((b.outlet_price) - (b.outlet_price / (1 + (b.gst / 100)))) / 2, 0)), 2) AS cgst,
ROUND(SUM(IF(b.gst_type = 'Inner GST', ((b.outlet_price) - (b.outlet_price / (1 + (b.gst / 100)))) / 2, 0)), 2) AS sgst

    FROM adv_order_mst a
    JOIN adv_order_det b ON a.id = b.mst_id
    JOIN outlet c ON a.outlet_id = c.id
    WHERE a.customer_type = 'outlet'
      AND (a.status = 'dispatch' OR a.status = 'delivered' or a.is_invoice=1)
      AND a.order_date BETWEEN ? AND ?
    GROUP BY a.order_date, a.id, c.outlet_name,a.order_id,a.is_invoice
";
        $params = [$fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt, $fromDt, $toDt];

        $data = DB::select($sql . " ORDER BY invoice_date LIMIT ? OFFSET ?", array_merge($params, [$limit, $offset]));

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
}
