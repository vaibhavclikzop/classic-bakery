<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Outlet extends Controller
{


    public function SaveOutlet(Request $request)
    {
        $validator = Validator::make($request->all(), [


            'outlet_name' => 'required',

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
              $outlet_id=  DB::table('outlet')->insertGetId(array(

                    "outlet_name" => $request->outlet_name,
                    "contact_person" => $request->contact_person,
                    "number" => $request->number,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "customer_type_id" => $request->customer_type_id,
                    "nickname" => $request->nickname,
                  


                ));

                DB::table('company_settings')->insertGetId(array(
                    
                 
                    "outlet_id" => $outlet_id,
        
                ));
                DB::table('outlet_users')->insertGetId(array(
                    
                    "name" => $request->contact_person,
                
                    "number" => $request->number,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "role_id" => 1,
                    "password" => $request->password,
                    "parent_id" => 0,
                    "outlet_id" => $outlet_id,
        
                ));
            } else {
                DB::table('outlet')->where("id", $request->id)->update(array(
                    "outlet_name" => $request->outlet_name,
                    "contact_person" => $request->contact_person,
                    "number" => $request->number,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "customer_type_id" => $request->customer_type_id,
                    "nickname" => $request->nickname,
                    
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function OutletRole(){
       $data= DB::table("outlet_role")->get();
       return view("outlet-role",compact("data"));
    }

    public function SaveOutletRole(Request $request){

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
                DB::table('outlet_role')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('outlet_role')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function OutletUserPermission(Request $request,$id){

        $role = DB::table("role")->where("id", $id)->first();


        $permission_mst = DB::table("outlet_permission as a")
            ->select("a.*")
            ->whereNotExists(function ($query) use ($role) {
                $query->select(DB::raw(1))
                    ->from("outlet_role_permission as b")
                    ->whereColumn("b.outlet_permission_id", "a.id")
                    ->where("b.outlet_role_id", $role->id);
            })
            ->get();



        $role_permission = DB::table("outlet_role_permission as a")
            ->select("a.*", "b.name as permission")
            ->join("outlet_permission as b", "a.outlet_permission_id", "b.id")
            ->where("a.outlet_role_id", $role->id)
            ->get();

        return view("outlet-user-permission", compact("role", "permission_mst", "role_permission", "id"));
    }

    public function SaveOutletUserPermission(Request $request){

        $validator = Validator::make($request->all(), [
            'outlet_role_id' => 'required',
            'outlet_permission_id' => 'required',
            'view' => 'required',
            'edit' => 'required',
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

        $role_permission = DB::table("outlet_role_permission")->where("outlet_role_id", $request->outlet_role_id)->where("outlet_permission_id", $request->outlet_permission_id)->first();
        if ($role_permission) {
            return  redirect()->back()->with("error", "User permission already added");
        }
        try {
            if (empty($request->id)) {
                DB::table('outlet_role_permission')->insertGetId(array(
                    "outlet_role_id" => $request->outlet_role_id,
                    "outlet_permission_id" => $request->outlet_permission_id,
                    "edit" => $request->edit,
                    "view" => $request->view,
                ));
            } else {
                DB::table('outlet_role_permission')->where("id", $request->id)->update(array(

                    "edit" => $request->edit,
                    "view" => $request->view,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function RemoveOutletPermission(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',

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

        DB::table('outlet_role_permission')->where("id", $request->id)->delete();
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
