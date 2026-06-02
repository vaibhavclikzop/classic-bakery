<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        // ================= BASE QUERY =================
        $base = DB::table('outward_customer_order_det as od')

            ->join('outward_customer_order_mst as om', 'od.mst_id', '=', 'om.id')

            ->join('order_mst as b', 'om.order_id', '=', 'b.id')

            ->join('order_det as a', function ($join) {
                $join->on('od.product_id', '=', 'a.product_id')
                    ->on('om.order_id', '=', 'a.mst_id');
            })

            ->leftJoin('outlet as o', function ($join) {
                $join->on('b.customer_id', '=', 'o.id')
                    ->where('b.order_type', 'outlet');
            })

            ->leftJoin('customers as cu', function ($join) {
                $join->on('b.customer_id', '=', 'cu.id')
                    ->where('b.order_type', 'customer');
            })

            ->whereBetween('om.invoice_date', [$fromDt, $toDt])

            ->when($order_type, function ($q) use ($order_type) {
                $q->where('b.order_type_id', $order_type);
            })

            ->when($f_product_sub_category, function ($q) use ($f_product_sub_category) {
                $q->join('finish_products_mst as c', 'od.product_id', '=', 'c.id')
                    ->where('c.f_sub_category_id', $f_product_sub_category);
            })

            ->select(
                'od.product_id',

                DB::raw("$partyCase as party_name"),

                // outward qty
                DB::raw('SUM(od.qty) as qty'),

                // actual sold qty
                DB::raw('SUM(od.qty) as sale_qty'),

                // outward amount
                DB::raw("
            ROUND(
                SUM(
                    (od.qty * a.price)
                    * (1 - a.discount / 100)
                ),
            2) as amount
        ")
            )

            ->groupBy(
                'od.product_id',
                DB::raw($partyCase)
            );


        // ================= FINAL DATA =================
        $rows = DB::query()
            ->fromSub($base, 'x')

            ->join('finish_products_mst as c', 'x.product_id', '=', 'c.id')
            ->join('f_product_category as fpc', 'c.f_category_id', '=', 'fpc.id')
            ->join('f_product_sub_category as fpsc', 'c.f_sub_category_id', '=', 'fpsc.id')

            ->when($f_product_sub_category, function ($q) use ($f_product_sub_category) {
                $q->where('fpsc.id', $f_product_sub_category);
            })

            ->select(
                'c.name as product',
                'fpc.name as category',
                'fpsc.name as sub_category',
                'x.party_name',

                DB::raw('SUM(x.qty) as order_qty'),
                DB::raw('SUM(x.sale_qty) as sale_qty'),
                DB::raw('SUM(x.amount) as amount')
            )

            ->groupBy(
                'c.name',
                'fpc.name',
                'fpsc.name',
                'x.party_name'
            )

            ->get();


        // ================= GET PARTIES =================
        $parties = $rows->pluck('party_name')->unique()->values();


        // ================= PIVOT FORMAT =================
        $result = [];

        foreach ($rows as $row) {

            $key = $row->product . '|' . $row->category . '|' . $row->sub_category;

            if (!isset($result[$key])) {

                $result[$key] = [
                    'product'      => $row->product,
                    'category'     => $row->category,
                    'sub_category' => $row->sub_category,
                ];
            }

            $safe = preg_replace('/[^A-Za-z0-9]/', '_', $row->party_name);

            $result[$key]["order_qty_$safe"]  = $row->order_qty;
            $result[$key]["sale_qty_$safe"]   = $row->sale_qty;
            $result[$key]["amount_$safe"]     = $row->amount;
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

    public function advanceOrderSaleReport(Request $request)
    {


        $fromDt = $request->fromDt ?? date("Y-m-d");
        $toDt   = $request->toDt ?? date("Y-m-d");

        $data = DB::table('adv_order_mst as m')

            ->join('adv_order_det as d', 'm.id', '=', 'd.mst_id')
            ->join('adv_order_item_mst as i', 'd.product_id', '=', 'i.id')

            // Outlet Join
            ->leftJoin('outlet as o', function ($join) {
                $join->on('m.outlet_id', '=', 'o.id')
                    ->where('m.customer_type', 'outlet');
            })

            // Customer Join
            ->leftJoin('customers as c', function ($join) {
                $join->on('m.outlet_id', '=', 'c.id')
                    ->where('m.customer_type', 'customer');
            })

            ->where('m.is_invoice', 1)

            ->when($fromDt && $toDt, function ($q) use ($fromDt, $toDt) {
                $q->whereBetween(DB::raw('DATE(m.delivery_date)'), [$fromDt, $toDt]);
            })

            ->select(

                'i.name as product_name',

                // Dynamic Name (Outlet / Customer)
                DB::raw("
            CASE 
                WHEN m.customer_type = 'outlet' THEN o.outlet_name
                WHEN m.customer_type = 'customer' THEN c.name
            END as party_name
        "),

                DB::raw('SUM(d.weight) as qty'),
                DB::raw('SUM(d.qty) as total_qty'),
                DB::raw('SUM(d.outlet_price) as amount')

            )

            ->groupBy(
                'i.name',
                DB::raw("
            CASE 
                WHEN m.customer_type = 'outlet' THEN o.outlet_name
                WHEN m.customer_type = 'customer' THEN c.name
            END
        ")
            )

            ->orderBy('i.name')

            ->get();



        $pivot = [];
        $parties = []; // renamed (because now it's outlet + customer)

        foreach ($data as $row) {

            // store dynamic columns (outlet + customer)
            $parties[$row->party_name] = $row->party_name;

            if (!isset($pivot[$row->product_name])) {
                $pivot[$row->product_name] = [];
            }

            $pivot[$row->product_name][$row->party_name] = [
                'total_qty' => $row->total_qty,
                'qty' => $row->qty,
                'amount' => $row->amount
            ];
        }
        return view("report.sale-report.advance-order-sale-report", compact('pivot', 'fromDt', 'toDt', "parties"));
    }
}
