<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{
    public function categoryWiseSaleDamage(Request $request)
    {
        $fromDt = request("fromDt");
        $toDt   = request("toDt");
        $order_type   = request("order_type");
        $f_product_sub_category   = request("f_product_sub_category");




        $partyCase = "(CASE 
        WHEN b.order_type = 'outlet' THEN o.outlet_name
        WHEN b.order_type = 'customer' THEN cu.name
    END)";


        $returnPartyCase = "(CASE 
        WHEN rm.order_type = 'outlet' THEN ro.outlet_name
        WHEN rm.order_type = 'customer' THEN rcu.name
    END)";


        $partiesQuery = DB::table('order_mst as b')

            ->join('order_det as a', 'a.mst_id', '=', 'b.id') // ✅ REQUIRED
            ->join('finish_products_mst as c', 'a.product_id', '=', 'c.id') // ✅ REQUIRED

            ->leftJoin('outlet as o', function ($join) {
                $join->on('b.customer_id', '=', 'o.id')
                    ->where('b.order_type', 'outlet');
            })

            ->leftJoin('customers as cu', function ($join) {
                $join->on('b.customer_id', '=', 'cu.id')
                    ->where('b.order_type', 'customer');
            })

            ->select(DB::raw("$partyCase as party_name"))
            ->whereNotNull('b.customer_id')
            ->whereBetween('b.delivery_date', [$fromDt, $toDt]);


        // ✅ order type filter (FIXED)
        if ($order_type) {
            $partiesQuery->where("b.order_type_id", $order_type);
        }


        // ✅ sub category filter (NOW WORKS)
        if ($f_product_sub_category) {
            $partiesQuery->where("c.f_sub_category_id", $f_product_sub_category);
        }

        $parties = $partiesQuery
            ->groupBy('party_name')
            ->pluck('party_name');





        $returnSub = DB::table('sale_return_det as rd')
            ->select(
                'rd.product_id',
                DB::raw("$returnPartyCase as party_name"),
                DB::raw("SUM(rd.qty) as return_qty")
            )
            ->join('sale_return_mst as rm', 'rd.mst_id', '=', 'rm.id')

            ->leftJoin('outlet as ro', function ($join) {
                $join->on('rm.customer_id', '=', 'ro.id')
                    ->where('rm.order_type', 'outlet');
            })

            ->leftJoin('customers as rcu', function ($join) {
                $join->on('rm.customer_id', '=', 'rcu.id')
                    ->where('rm.order_type', 'customer');
            })

            ->whereBetween('rm.return_date', [$fromDt, $toDt])  
            ->where("rm.status","complete")
            ->groupBy('rd.product_id', 'party_name');


        $select = [
            'c.name as product',
            'fpc.name as category',
            'fpsc.name as sub_category'
        ];

        foreach ($parties as $party) {

            $safe = preg_replace('/[^A-Za-z0-9]/', '_', $party);

            $select[] = DB::raw("SUM(CASE WHEN $partyCase = '$party' THEN a.qty ELSE 0 END) as order_qty_$safe");

            $select[] = DB::raw("SUM(CASE WHEN $partyCase = '$party' THEN a.booked_qty ELSE 0 END) as sale_qty_$safe");

            $select[] = DB::raw("SUM(CASE WHEN $partyCase = '$party' THEN a.price ELSE 0 END) as amount_$safe");


            $select[] = DB::raw("SUM(CASE WHEN r.party_name = '$party' THEN r.return_qty ELSE 0 END) as return_qty_$safe");
        }


        $filter = DB::table('order_det as a')
            ->select($select)

            ->join("order_mst as b", "a.mst_id", "b.id")
            ->join("finish_products_mst as c", "a.product_id", "c.id")
            ->join("f_product_category as fpc", "c.f_category_id", "fpc.id")
            ->join("f_product_sub_category as fpsc", "c.f_sub_category_id", "fpsc.id")

            ->leftJoin('outlet as o', function ($join) {
                $join->on('b.customer_id', '=', 'o.id')
                    ->where('b.order_type', 'outlet');
            })

            ->leftJoin('customers as cu', function ($join) {
                $join->on('b.customer_id', '=', 'cu.id')
                    ->where('b.order_type', 'customer');
            })


            ->leftJoinSub($returnSub, 'r', function ($join) {
                $join->on('r.product_id', '=', 'c.id');
            })


            ->whereBetween('b.delivery_date', [$fromDt, $toDt]);
        if ($order_type) {
            $filter->where("b.order_type_id", $order_type);
        }

        if ($f_product_sub_category) {
            $filter->where("fpsc.id", $f_product_sub_category);
        }


        $data =  $filter->groupBy("c.name", "fpc.name", "fpsc.name")
            ->get();


        $order_type =  DB::table("order_type")->get();
        $f_product_sub_category =  DB::table("f_product_sub_category")->get();

        return view("report.sale-report.category-wise-sale-and-damage", compact("data", "parties", "f_product_sub_category", "order_type"));
    }
}
