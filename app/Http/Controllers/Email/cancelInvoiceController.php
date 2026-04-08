<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Mail\SendCancelInvoiceOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class cancelInvoiceController extends Controller
{
    public function sendCancelInvoiceOTP(Request $request)
    {

        try {
            $otp = rand(100000, 999999);
            session(['otp' => $otp]);
            Mail::to("singh.dashmeet007@gmail.com")->send(new SendCancelInvoiceOTP($otp));
            return response()->json(['status' => true, 'message' => 'OTP sent']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function verifyCancelOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'otp' => 'required',


        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        try {
            $otp = $request->otp;
            if (session("otp") == $otp) {
                session()->forget('otp');
                return response()->json(['status' => true, 'message' => "Successfully Verified"]);
            } else {
                return response()->json(['status' => false, 'message' => "Invalid or wrong OTP"]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function cancelRegularInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',


        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            DB::table('outward_customer_order_mst')->where("id", $request->id)->update(array(
                "status" => "cancel",
            ));
            return redirect()->back()->with('success', "Save Successfully");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
