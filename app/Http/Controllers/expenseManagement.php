<?php

namespace App\Http\Controllers;

use App\Models\expense_category_det;
use App\Models\expense_category_mst;
use App\Models\expense_sub_category_mst;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class expenseManagement extends Controller
{
    public function expenseCategory(Request $request)
    {
        $data =  expense_category_mst::get();
        return view("expense-category", compact("data"));
    }

    public function saveExpenseCategory(Request $request)
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
            if ($request->id) {
                $data = expense_category_mst::where("id", $request->id)->first();
                $data->name = $request->name;
                $data->save();
            } else {
                $data = new expense_category_mst();
                $data->name = $request->name;
                $data->save();


                $outlet =  DB::table('outlet')->get();
                foreach ($outlet as $key => $value) {
                    DB::table("expense_category")->insert(array(
                        "name" => $request->name,
                        "active" => 1,
                        "outlet_id" => $value->id,
                        "exp_cat_mst_id" => $data->id,
                    ));
                }
            }
        } catch (Exception $e) {



            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function expenseSubCategory(Request $request)
    {
        $category =  expense_category_mst::get();
        $data = expense_sub_category_mst::with("categoryDetails")->get();
        return view("expense-sub-category", compact("data", "category"));
    }

    public function saveExpenseSubCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
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
            if ($request->id) {
                $data = expense_sub_category_mst::where("id", $request->id)->first();
                $data->category_id = $request->category_id;
                $data->name = $request->name;
                $data->save();
            } else {

                $data = new expense_sub_category_mst();
                $data->category_id = $request->category_id;
                $data->name = $request->name;
                $data->save();


                $expense_category =  DB::table('expense_category')->where("exp_cat_mst_id", $data->category_id)->get();
                foreach ($expense_category as $key => $value) {
                    DB::table("expense_subcategory")->insert(array(
                        "name" => $request->name,
                        "active" => 1,
                        "outlet_id" => $value->outlet_id,
                        "category_id" => $value->id,
                    ));
                }
            }
        } catch (Exception $e) {



            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function expense(Request $request)
    {
        $fromDt   = $request->input('fromDt');
        $toDt     = $request->input('toDt');
        $outlet_id = $request->input('outlet_id');

        $outlet = DB::table('outlet')->get();

        $query = DB::table('expense as e')
            ->leftJoin('expense_category as c', 'e.expense_cat_id', 'c.id')
            ->leftJoin('expense_subcategory as sc', 'e.expense_subcat_id', 'sc.id')
            ->leftJoin('outlet as o', 'e.outlet_id', 'o.id')

            ->select(
                'e.*',
                'c.name as category_name',
                'sc.name as sub_category_name',
                'o.outlet_name as outlet_name'
            );

            $query->where('e.outlet_id', $outlet_id);
    

        if ($fromDt) {
            $query->whereDate('e.expense_date', '>=', $fromDt);
        }

        if ($toDt) {
            $query->whereDate('e.expense_date', '<=', $toDt);
        }

        $data = $query->orderBy('e.expense_date', 'desc')->get();

        return view('expense', compact('data', 'outlet'));
    }
}
