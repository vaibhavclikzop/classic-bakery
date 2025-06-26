<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Admin extends Controller
{
    public function Dashboard(Request $request)
    {


        $customers = DB::table("customers")->get()->count();
        $vendor = DB::table("vendor")->get()->count();


       


        $total_delivered = DB::table("order_mst")->where("status", "delivered")->get()->count();
        $total_pending = DB::table("order_mst")->where("status", "pending")->get()->count();
      
        $this_month_delivered = DB::table("order_mst")->where("status", "delivered")->whereMonth("delivery_date", now())->get()->count();


        $products = DB::table("products")->get()->count();

        $order_mst = DB::table("order_mst")->get()->count();


        $minimum_stock = DB::table("current_stock as a")
            ->select("a.*", "b.name as product",   "b.article_no", "b.min_stock")
            ->join("products as b", "a.product_id", "=", "b.id")
           
            ->whereRaw("a.stock <= b.min_stock")->get()->count();



        $total_sale_amt = DB::table("order_det")->select(DB::raw("SUM(price * qty) as total_purchase_amt"))
            ->value('total_sale_amt');


        $recent_order = DB::table("order_mst as a")
            ->select("a.*", "c.name as customer")
            ->join("customers as c", "a.customer_id", "c.id")
            ->orderby("id", "desc")->limit(4)->get();

 





        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $complete = DB::table('finish_products_mst')
            ->selectRaw('DATE_FORMAT(created_at, "%M") as month_name, COUNT(*) as total')
        
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%M")'))
            ->pluck('total', 'month_name');

        // Fill the months that are not in the database with 0
        $completeResult = collect($months)->mapWithKeys(function ($month) use ($complete) {
            return [$month => $complete->get($month, 0)];
        });


        $delivered = DB::table('order_mst')
            ->selectRaw('DATE_FORMAT(delivery_date, "%M") as month_name, COUNT(*) as total')
            ->where('status', 'delivered')
            ->whereYear('delivery_date', now()->year)
            ->groupBy(DB::raw('DATE_FORMAT(delivery_date, "%M")'))
            ->pluck('total', 'month_name');

        $delivered_result = collect($months)->mapWithKeys(function ($month) use ($delivered) {
            return [$month => $delivered->get($month, 0)];
        });


        return view("dashboard", compact("customers", "vendor", "products", "order_mst", "total_delivered", "total_pending", "this_month_delivered", "recent_order",  "minimum_stock", "months", "delivered_result","completeResult"));
    }


    public function StartDay(Request $request)
    {

        $attendance = DB::table("attendance")->where("emp_id", $request->user->id)->whereDate('start_time', now())->first();
        if ($attendance) {
            return redirect()->back()->with("error", "Day already started");
        } else {


            $mst_id = DB::table('attendance')->insertGetId(array(
                "start_location" => $request->start_location,
                "emp_id" => $request->user->id,
            ));
            return redirect()->back()->with("success", "Day started successfully");
        }
    }

    public function EndDay(Request $request)
    {
        $attendance = DB::table("attendance")->where("emp_id", $request->user->id)->whereDate('start_time', now())->first();
        if ($attendance && $attendance->start_time && empty($attendance->end_time)) {
            $mst_id = DB::table('attendance')->where("id",$attendance->id)->update(array(
                "end_location" => $request->start_location,
                "end_time" => now(),
                
            ));
            return redirect()->back()->with("success", "Day ended successfully");
        } else if($attendance && $attendance->start_time && $attendance->end_time){
            return redirect()->back()->with("error", "Already Ended");
        }else if(empty($attendance)){
            return redirect()->back()->with("error", "Day not started yet.");
        }
    }

    public function Profile(Request $request){
        $user= DB::table("users")->where("id",$request->user->id)->first();
        return view("profile",compact("user"));

    }

    public function SaveProfile(Request $request){
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'email' => 'required',
            'phone' => 'required|min:10|max:10',
            'address' => 'required',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }
        DB::table('users')->where("id", $request->user->id)->update(array(
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "address" => $request->address,
            "password" => $request->password,

        ));
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
