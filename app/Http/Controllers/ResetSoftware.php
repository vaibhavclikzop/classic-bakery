<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;


class ResetSoftware extends Controller
{
    public function ResetSoftware(Request $request, $key)
    {

        $reset_key = DB::table("reset_key")->where("reset_key", $key)->first();
        if ($reset_key) {

            return view("reset-software", compact("reset_key"));
        } else {
            return redirect("/")->with("error", "Incorrect Reset Key");
        }
    }

    public function ResetSoft(Request $request)
    {
        $reset_key = DB::table("reset_key")->where("reset_key", $request->reset_key)->first();
        if ($reset_key) {

            DB::table("company_settings")->where("id", 1)->update(array(
                "company_name" => "Clikzop Innovations",
                "address" => "office no 38,  sushma infinium, Ambala chandigarh highway",
                "contact_person" => "Clikzop innovation",
                "number" => "9876543210",
                "email" => "clikzopinnovations@gmail.com",
                "gst_no" => "GSTIN321654987",
                "img" => "1722883763.jpg"
            ));
            DB::statement('TRUNCATE TABLE category');
            DB::statement('TRUNCATE TABLE company');
            DB::statement('TRUNCATE TABLE current_stock');
            DB::statement('TRUNCATE TABLE current_stock_genset');
            DB::statement('TRUNCATE TABLE customers');
            DB::statement('TRUNCATE TABLE finish_products_det');
            DB::statement('TRUNCATE TABLE finish_products_mst');
            DB::statement('TRUNCATE TABLE gen_set_det');
            DB::statement('TRUNCATE TABLE gen_set_mst');
            DB::statement('TRUNCATE TABLE order_det');
            DB::statement('TRUNCATE TABLE order_mst');
            DB::statement('TRUNCATE TABLE po_det');
            DB::statement('TRUNCATE TABLE po_mst');
            DB::statement('TRUNCATE TABLE products');
            DB::statement('TRUNCATE TABLE stock_inward_det');
            DB::statement('TRUNCATE TABLE stock_inward_mst');
            DB::statement('TRUNCATE TABLE store');
    
 
            DB::statement('TRUNCATE TABLE team_member');
            DB::statement('TRUNCATE TABLE unit_type');
            DB::statement('TRUNCATE TABLE vendor');
            DB::statement('TRUNCATE TABLE vendor_type');
            DB::statement('TRUNCATE TABLE users');
            DB::statement('TRUNCATE TABLE lead');
            DB::statement('TRUNCATE TABLE lead_remarks');

            DB::table("users")->insertGetId(array(
                "name" => "Clikzop Expertz",
                "address" => "office no 38, Sushma infinium ",
                "email" => "admin@gmail.com",
                "password" => "123456",
                "user_type" => "admin",
                "role_id" => 1,
            ));

            DB::table("reset_key")->where("id", 1)->update(array(
                "reset_key" => bin2hex(random_bytes(16)),

            ));

            echo "<h3>Your software reset successfully. </h3> <a href='/'>Home</a>";
        } else {
            return redirect("/")->with("error", "Incorrect Reset Key");
        }
    }
}
