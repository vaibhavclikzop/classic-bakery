<?php

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

if (!function_exists('CheckUserPassword')) {
    function CheckUserPassword($param)
    {
        $users =  DB::table("users")->where("password", $param)->first();
        if ($users) {
            return $users;
        } else {
            return false;
        }
    }
}

if (!function_exists('formatPriceQty')) {
    function formatQtyPrice($v)
    {
        $v = floatval($v);
        return number_format($v, 2, '.', '');
    }
}


if (!function_exists('getInvoiceNoOld')) {
    function getInvoiceNoOld()
    {
        $invoice_prefix = DB::table("company_settings")->where("id", 1)->first();
        $out_inv_no =   DB::table("outward_customer_order_mst")->whereDate("created_at", now())->count();
        $adv_inv_no =   DB::table("adv_order_mst")->whereDate("created_at", now())->where("is_invoice", 1)->count();
        $inv_no = $out_inv_no + $adv_inv_no;
        if (!$inv_no) {
            $inv_no = 1;
        } else {
            $inv_no++;
        }

        do {
            $order_no = $invoice_prefix->order_prefix . date('d-m-y') . "-" . $inv_no;

            $exists = DB::table("outward_customer_order_mst")
                ->where("order_no", $order_no)
                ->exists()
                || DB::table("adv_order_mst")
                ->where("order_id", $order_no)
                ->exists();

            $inv_no++;
        } while ($exists);

        return $order_no;
    }
}



if (!function_exists('getInvoiceNo')) {
    function getInvoiceNo()
    {
        $invoice_prefix = DB::table("company_settings")->where("id", 1)->first();

        $today = now();

        // ✅ Financial Year
        if ($today->month >= 4) {
            $startYear = $today->year;
            $endYear = $today->year + 1;
        } else {
            $startYear = $today->year - 1;
            $endYear = $today->year;
        }

        $financialYear = $startYear . "-" . substr($endYear, -2);

        // ✅ Check if any invoice already exists in this FY
        $regularExistsInFY = DB::table("outward_customer_order_mst")
            ->where("financial_year", "=", $financialYear)
            ->exists();

        $advExistsInFY = DB::table("adv_order_mst")
            ->where("financial_year", "=", $financialYear)
            ->exists();

        if ($regularExistsInFY || $advExistsInFY) {

            // ✅ Get max number from THIS FY
            $maxRegular = DB::table("outward_customer_order_mst")
                ->where("financial_year", "=", $financialYear)
                ->count("id");

            $maxAdv = DB::table("adv_order_mst")
                ->where("financial_year", "=", $financialYear)
                ->where("is_invoice", 1)
                ->count("id");
            $nextNumber = $maxRegular + $maxAdv + 1;
        } else {

            $nextNumber = 1;
        }

        // ✅ Generate invoice
        do {
            $order_no = $invoice_prefix->order_prefix . date('d-m-y') . "-" . $nextNumber;
            $exists = DB::table("outward_customer_order_mst")
                ->where("order_no", $order_no)
                ->exists()
                || DB::table("adv_order_mst")
                ->where("order_id", $order_no)
                ->exists();

            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);
        // echo $order_no;
        // die;
        return $order_no;
    }
    if (!function_exists('myDateFormat')) {
        function myDateFormat($date)
        {
            return date("d-m-Y", strtotime($date));
        }
    }
}
