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


    public function poGeneratedReport(Request $request)
    {

        $po = $request->input('po', 1);
        $fromDt = request("fromDt") ?? date("Y-m-d");
        $toDt = request("toDt") ?? date("Y-m-d");

        $po = DB::table("po_mst as a")
            ->select("a.*", "b.name as vendor_name", "c.name as user_name")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("users as c", "a.user_id", "c.id");


        if ($fromDt) {
            $po->whereDate("a.created_at", ">=", $fromDt);
        }
        if ($toDt) {
            $po->whereDate("a.created_at", "<=", $toDt);
        }

        $po_mst =  $po->orderBy("a.id", "desc")
            ->get();
        return view("report.po-generated-report", compact("po_mst"));
    }


    public function rmPurchaseHistoryReport(Request $request)
    {
        $fromDt = $request->input("fromDt")  ;
        $toDt   = $request->input("toDt")  ;
        $product_id   = $request->input("product_id");

        $query = DB::table("stock_inward_mst as a")
            ->select(
                "c.name as product_name",
                "a.received_material_date as date",
                "v.name as vendor_name",
                "b.price",
                DB::raw("SUM(b.qty) as qty")
            )
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("products as c", "b.product_id", "c.id")
            ->join("vendor as v", "a.vendor_id", "v.id")
            ->where("c.id",$product_id)
            ;

        if ($fromDt) {
            $query->whereDate("a.received_material_date", ">=", $fromDt);
        }

        if ($toDt) {
            $query->whereDate("a.received_material_date", "<=", $toDt);
        }

        $data = $query
            ->groupBy(
                "c.name",
                "a.received_material_date",
                "v.name",
                "b.price"
            )
            ->orderBy("a.received_material_date", "desc")
            ->get();

         $products=   DB::table("products")->get();

        return view("report.rm-purchase-history-report", compact("data","products"));
    }



    public function departmentSaleReport(Request $request)
    {
        $department_id = $request->input("department_id");
        $category_id   = $request->input("category_id");
        $fromDt = $request->input("fromDt") ?? date("Y-m-d");
        $toDt   = $request->input("toDt") ?? date("Y-m-d");

        $department = DB::table('department')->get();

        $categories = DB::table('finish_products_mst')
            ->select('f_category_id')
            ->distinct()
            ->get();


        $data = DB::table("order_det as od")
            ->join("finish_products_mst as fp", "od.product_id", "fp.id")

            ->join("f_product_sub_category as fsc", "f_sub_category_id", "fsc.id")

            ->select(
                "fsc.name as sub_category",
                DB::raw("SUM(od.booked_qty) as qty"),
                DB::raw("SUM(od.booked_qty * od.price) as sale_amount")
            );

        // Filters
        if ($department_id) {
            $data->where("fp.department_id", $department_id);
        }

        if ($category_id) {
            $data->where("fp.f_category_id", $category_id);
        }

        if ($fromDt) {
            $data->whereDate("od.created_at", ">=", $fromDt);
        }

        if ($toDt) {
            $data->whereDate("od.created_at", "<=", $toDt);
        }

        $data = $data
            ->groupBy("fsc.name")
            ->orderBy("fsc.name", "asc")
            ->get();

        return view("report.department-sale-report", compact("data", "department", "categories"));
    }


    public function rmProductledgerReport(Request $request)
    {
        $product_id = $request->input("product_id");

        $fromDt = $request->input("fromDt") ?? date("Y-m-d");
        $toDt   = $request->input("toDt") ?? date("Y-m-d");

        $products = DB::table('products')->get();


        $inward = DB::table("stock_inward_mst as a")
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as v", "a.vendor_id", "v.id")
            ->select(
                "a.received_material_date as date",
                "a.created_at as created_at",
                "b.type as type",
                "v.name as particular",
                "b.qty as in_qty",
                DB::raw("0 as out_qty")
            )->where("b.type", "raw material");

        $outward = DB::table("outward_mst as o")
            ->join("outward_det as od", "o.id", "od.mst_id")
            ->join("products as p", "od.product_id", "p.id")
            ->join("department as d", "o.department_id", "d.id")

            ->select(
                "o.created_at as date",
                "o.created_at as created_at",
                DB::raw("'Issue' as type"),
                "d.name as particular",
                DB::raw("0 as in_qty"),
                "od.qty as out_qty"
            );


        $inward->where("b.product_id", $product_id);
        $outward->where("od.product_id", $product_id);



        $inward->whereDate("a.received_material_date", ">=", $fromDt);
        $outward->whereDate("o.created_at", ">=", $fromDt);



        $inward->whereDate("a.received_material_date", "<=", $toDt);
        $outward->whereDate("o.created_at", "<=", $toDt);



        $union = $inward->unionAll($outward);

        $data = DB::table(DB::raw("({$union->toSql()}) as ledger"))
            ->mergeBindings($union)
            ->orderBy("created_at", "asc")
            ->get();

        $balance = 0;
        foreach ($data as $row) {
            $balance += $row->in_qty;
            $balance -= $row->out_qty;
            $row->balance = $balance;
        }

        return view("report.rm-product-ledger-report", compact("data", "products"));
    }

    public function reOrderReport(Request $request)
    {

        $data = DB::table("vendor as a")
            ->select(
                "a.company_name as vendor",
                "c.name as product",
                "c.re_order_qty",
                "c.min_stock",
                DB::raw("COALESCE(d.stock, 0) as stock"),
                "e.name as sub_category",
            )
            ->join("vendor_product as b", "a.id", "b.vendor_id")
            ->join("products as c", "b.product_id", "c.id")


            ->leftJoin("current_stock as d", "c.id", "d.product_id")

            ->join("sub_category as e", "c.sub_category_id", "e.id")
            ->where("a.id", request("vendor_id"))
            ->get();
        $vendor =  DB::table('vendor')->get();
        return view("report.re-order-report", compact("data", "vendor"));
    }
}
