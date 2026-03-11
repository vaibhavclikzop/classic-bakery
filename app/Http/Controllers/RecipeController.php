<?php

namespace App\Http\Controllers;

use App\Models\finish_products_mst;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stichoza\GoogleTranslate\GoogleTranslate;

class RecipeController extends Controller
{
    public function createRecipe(Request $request)
    {
        $products =  products::with("unitType")->get();
        $department = DB::table('department')->orderBy("name", "ASC")->get();
        $finish_products_mst =  finish_products_mst::get();
        return view("create-recipe", compact("products", "department", "finish_products_mst"));
    }

    public function saveRecipe(Request $request)
    {

        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }

        DB::beginTransaction();
        try {
            $wo_no = 'WO_' . date('dmyhis');
            $mst_id = DB::table("recipe_mst")->insertGetId(array(
                "name" => $request->name,
                "department_id" => $request->department_id,
                "description" => $request->description,
                "batch" => $request->batch,
                "wo_no" => $wo_no,
                "user_id" => $request->user->id,
            ));

            foreach ($prod_list as $key => $value) {


                DB::table("recipe_det")->insert(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "uom" => $value->uom,
                ));
            }
            DB::commit();
            return redirect("recipe-view/" . $mst_id)->with('success', "Save Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }


    public function recipeList(Request $request)
    {
        $data = DB::table('recipe_mst as a')
            ->select("a.*", "d.name as dname")
            ->Leftjoin('department as d', "a.department_id", "d.id")
            ->orderBy("a.id", "desc")->get();
        return view("recipe-list", compact("data"));
    }

    public function recipeView(Request $request, $id)
    {
        $data = DB::table("recipe_mst as a")
            ->select("a.*", "d.name as dname")
            ->Leftjoin('department as d', "a.department_id", "d.id")
            ->where("a.id", $id)->first();
        $det =  DB::table("recipe_det as a")
            ->select("a.*", "b.name as product", "c.name as category")
            ->join("products as b", "a.product_id", "b.id")
            ->join("category as c", "b.category_id", "c.id")
            ->where("a.mst_id", $id)->get();
        return view("recipe-view", compact("data", "det"));
    }

    public function makeRecipe(Request $request, $id)
    {

        $qty = request("qty", 1);


        $data = DB::table("recipe_mst as a")
            ->select("a.*", "d.name as dname")
            ->Leftjoin('department as d', "a.department_id", "d.id")
            ->where("a.id", $id)->first();
        $det = DB::table("recipe_det as a")
            ->select(
                "a.*",
                "b.name as product",
                "c.name as category",
                "b.hindi",
                DB::raw("COALESCE(d.price, b.price) as price")
            )
            ->join("products as b", "a.product_id", "b.id")
            ->join("category as c", "b.category_id", "c.id")
            ->leftJoin("stock_inward_det as d", function ($join) {
                $join->on("b.id", "=", "d.product_id")
                    ->where("d.type", "raw_material");
            })
            ->where("a.mst_id", $id)
            ->orderBy("d.id", "desc")
            ->get();

        return view("make-recipe", compact("data", "det"));
    }
    public function receipeDelete(Request $request)
    {
        $id = $request->id;
        DB::table('recipe_det')->where('mst_id', $id)->delete();
        DB::table('recipe_mst')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Recipe deleted"
        ]);
    }
}
