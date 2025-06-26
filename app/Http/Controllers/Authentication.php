<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class Authentication extends Controller
{
    public function SuperAdmin()
    {
        if (!empty(session("token"))) {
            $superAdmin =   DB::table("users")->where("token", session("token"))->first();
            if (!empty($superAdmin)) {

                return redirect("dashboard");
            }
        }

        return view("login");
    }

    public function SuperAdminLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            session()->flash("error", "Enter email or password");
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $superAdmin =   DB::table("users")->where("email", $request->email)->where("password", $request->password)->first();
            if (!empty($superAdmin)) {
                $token = bin2hex(random_bytes(16));
                $agent = new Agent();
                $browser = $agent->browser();
                $version = $agent->version($browser);
                $platform = $agent->platform();
                DB::table('users')->where("id", $superAdmin->id)->update(array(
                    'token' => $token,
                    "last_ip" => $_SERVER['REMOTE_ADDR'],
                    'last_login' => date("Y-m-d H:m:s"),
                    'platform' => $browser . " / " . $version . ' / ' . $platform,
                ));
                session()->put('token', $token);
                session()->put('user', $superAdmin);
            } else {
                return redirect()->back()->with('error', "Incorrect Email or Password");
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        session()->flash('success', "login successfully");

        return redirect("dashboard");
    }


    public function logout(Request $request)
    {

        DB::table('users')->where("token", session("token"))->update(array(
            'token' => "",

        ));
        return redirect()->back()->with("success", "logout successfully");
    }
}
