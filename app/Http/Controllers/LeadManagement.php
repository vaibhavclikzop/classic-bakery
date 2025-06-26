<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class LeadManagement extends Controller
{
    public function Status(Request $request)
    {

        $status = DB::table("status")->get();
        return view("status", compact("status"));
    }

    public function SaveStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',

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

        try {
            if (empty($request->id)) {
                DB::table('status')->insertGetId(array(
                    "name" => $request->name,

                ));
            } else {
                DB::table('status')->where("id", $request->id)->update(array(
                    "name" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Lead(Request $request, $id)
    {

        $finish_products_mst = DB::table("finish_products_mst")->get();
        $lead = DB::table("lead as a")
        ->select("a.*","b.name as genset_name","c.name as status_name","d.name as user_name")
        ->join("finish_products_mst as b","a.gen_set","b.id")
        ->join("status as c","a.status","c.id")
        ->join("users as d","a.user_id","d.id")
        ->orderBy("a.id","desc")
        ->get();
        
        return view("lead", compact("finish_products_mst","lead"));
    }

    public function SaveLead(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'number' => 'required|min:10|max:10',
            'gen_set' => 'required',
            'remarks' => 'required',
            'status' => 'required|integer',

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
        $lead_id = 0;
        try {
            if (empty($request->id)) {
                $lead_id =    DB::table('lead')->insertGetId(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gen_set" => $request->gen_set,
                    "classification" => $request->classification,
                    "status" => $request->status,
                    "remind_date" => $request->remind_date,
                    "remind_time" => $request->remind_time,
                    "remarks" => $request->remarks,
                    "user_id" => $request->user->id,

                ));
            } else {
                 DB::table('lead')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gen_set" => $request->gen_set,
                    "classification" => $request->classification,
                    "status" => $request->status,
                    "remind_date" => $request->remind_date,
                    "remind_time" => $request->remind_time,
                    "remarks" => $request->remarks,
                    "user_id" => $request->user->id,

                ));

                $lead_id = $request->id;
            }

            DB::table('lead_remarks')->insertGetId(array(

                "lead_id" => $lead_id,
                "status" => $request->status,
                "remind_date" => $request->remind_date,
                "remind_time" => $request->remind_time,
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,

            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function GetLeadDetails(Request $request){
        $lead= DB::table("lead")->where("id",$request->id)->first();
        return $lead;

    }

    public function GetRemarks(Request $request){
        $lead_remarks= DB::table("lead_remarks as a")
        ->select("a.*","b.name as user","c.name as status")
        ->join("users as b","a.user_id","b.id")
        ->join("status as c","a.status","c.id")
        ->where("a.lead_id",$request->id)->get();
        return $lead_remarks;

    }
}
