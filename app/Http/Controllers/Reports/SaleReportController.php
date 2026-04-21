<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{
public function categoryWiseSaleDamage(Request $request)
{
    $fromDt = $request->fromDt ?? date("Y-m-d");
    $toDt   = $request->toDt ?? date("Y-m-d");
    $order_type = $request->order_type;
    $f_product_sub_category = $request->f_product_sub_category;

    // ================= PARTY CASE =================
    $partyCase = "(CASE 
        WHEN b.order_type = 'outlet' THEN o.outlet_name
        WHEN b.order_type = 'customer' THEN cu.name
    END)";

    // ================= BASE QUERY (NO DUPLICATION) =================
    $base = DB::table('order_det as a')
        ->select(
            'a.product_id',
            DB::raw("$partyCase as party_name"),
            DB::raw("SUM(a.qty) as qty"),
            DB::raw("SUM(a.booked_qty) as sale_qty"),
            DB::raw("SUM(a.price * a.qty) as amount")
        )
        ->join("order_mst as b", "a.mst_id", "b.id")

        ->leftJoin('outlet as o', function ($join) {
            $join->on('b.customer_id', '=', 'o.id')
                ->where('b.order_type', 'outlet');
        })

        ->leftJoin('customers as cu', function ($join) {
            $join->on('b.customer_id', '=', 'cu.id')
                ->where('b.order_type', 'customer');
        })

        ->whereBetween('b.delivery_date', [$fromDt, $toDt])

        ->when($order_type, fn($q) => $q->where("b.order_type_id", $order_type))

        ->when($f_product_sub_category, function ($q) use ($f_product_sub_category) {
            $q->join("finish_products_mst as c", "a.product_id", "c.id")
              ->where("c.f_sub_category_id", $f_product_sub_category);
        })

        ->groupBy('a.product_id', 'party_name');

    // ================= FINAL DATA =================
    $rows = DB::query()
        ->fromSub($base, 'x')

        ->join("finish_products_mst as c", "x.product_id", "c.id")
        ->join("f_product_category as fpc", "c.f_category_id", "fpc.id")
        ->join("f_product_sub_category as fpsc", "c.f_sub_category_id", "fpsc.id")

        ->when($f_product_sub_category, fn($q) => $q->where("fpsc.id", $f_product_sub_category))

        ->select(
            'c.name as product',
            'fpc.name as category',
            'fpsc.name as sub_category',
            'x.party_name',

            DB::raw("SUM(x.qty) as order_qty"),
            DB::raw("SUM(x.sale_qty) as sale_qty"),
            DB::raw("SUM(x.amount) as amount")
        )

        ->groupBy("c.name", "fpc.name", "fpsc.name", "x.party_name")
        ->get();

    // ================= GET PARTIES FROM DATA ONLY =================
    $parties = $rows->pluck('party_name')->unique()->values();

    // ================= PIVOT FORMAT =================
    $result = [];

    foreach ($rows as $row) {

        $key = $row->product . '|' . $row->category . '|' . $row->sub_category;

        if (!isset($result[$key])) {
            $result[$key] = [
                'product' => $row->product,
                'category' => $row->category,
                'sub_category' => $row->sub_category
            ];
        }

        $safe = preg_replace('/[^A-Za-z0-9]/', '_', $row->party_name);

        $result[$key]["order_qty_$safe"] = $row->order_qty;
        $result[$key]["sale_qty_$safe"]  = $row->sale_qty;
        $result[$key]["amount_$safe"]    = $row->amount;

        // return optional (set 0 for now)
        $result[$key]["return_qty_$safe"] = 0;
    }

    $result = array_values($result);

    $order_type = DB::table("order_type")->get();
    $f_product_sub_category = DB::table("f_product_sub_category")->get();

    return view("report.sale-report.category-wise-sale-and-damage", [
        "data" => $result,
        "parties" => $parties,
        "order_type" => $order_type,
        "f_product_sub_category" => $f_product_sub_category
    ]);
}

}
