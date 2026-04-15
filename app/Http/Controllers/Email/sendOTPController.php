<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Mail\sendOTPs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class sendOTPController extends Controller
{
    public function sendStockUpdateOTP(Request $request)
    {

        try {
            $otp = rand(100000, 999999);
            session(['otp' => $otp]);
            Mail::to("singh.dashmeet007@gmail.com")->send(new sendOTPs($otp, "Update Outlet Stock"));
            //Mail::to("vaibhav@clikzopinnovations.com")->send(new sendOTPs($otp, "Update Outlet Stock"));
            return response()->json(['status' => true, 'message' => 'OTP sent']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function updateOutletStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required',
            'product_id' => 'required',


        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {



            foreach ($request->product_id as $key => $value) {
                if ($value[0] != 0) {
                    DB::table("outlet_current_stock")->where("id", $key)->increment("stock", $value[0]);
                    DB::table("outlet_current_stock_adjustment")->insert(array(
                        "cs_id" => $key,
                        "qty" => $value[0],
                    ));
                }
            }

            return redirect()->back()->with('success', "Save Successfully");
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function sendDeleteDuplicateOTP(Request $request)
    {
        try {
            $otp = rand(100000, 999999);
            session(['otp' => $otp]);
             Mail::to("singh.dashmeet007@gmail.com")->send(new sendOTPs($otp, "Update Outlet Stock"));
            //Mail::to("vaibhav@clikzopinnovations.com")->send(new sendOTPs($otp, "Delete Duplicate Product"));
            return response()->json(['status' => true, 'message' => 'OTP sent']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function deleteOutletCSDuplicate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required',



        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {



            DB::delete("
    DELETE a
    FROM outlet_current_stock a
    JOIN outlet_current_stock b
        ON a.product_id = b.product_id
        AND a.outlet_id = b.outlet_id
        AND a.id > b.id
    WHERE a.outlet_id = ?
", [$request->outlet_id]);


            return redirect()->back()->with('success', "Save Successfully");
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
