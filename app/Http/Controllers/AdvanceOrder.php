<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;


class AdvanceOrder extends Controller
{
    public function AdvanceOrderCategory(Request $request)
    {
        $data = DB::table("adv_order_category")->get();
        return view("advance-order-category", compact("data"));
    }

    public function SaveAdvanceOrderCategory(Request $request)
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
                DB::table('adv_order_category')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('adv_order_category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function AdvanceOrderFlavour(Request $request)
    {
        $data = DB::table("adv_order_flavour")->get();
        return view("advance-order-flavour", compact("data"));
    }

    public function SaveAdvanceOrderFlavour(Request $request)
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
                $mst_id = DB::table('adv_order_flavour')->insertGetId(array(
                    "name" => $request->name,
                ));

                $adv_order_item_mst =  DB::table("adv_order_item_mst")->get();
                foreach ($adv_order_item_mst as $key => $value) {
                    DB::table("adv_order_item_det")->insertGetId(array(
                        "mst_id" => $value->id,
                        "flavour_id" => $mst_id,
                    ));
                }
            } else {
                DB::table('adv_order_flavour')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function AdvanceOrderShape(Request $request)
    {
        $data = DB::table("adv_order_shape")->get();
        return view("advance-order-shape", compact("data"));
    }

    public function SaveAdvanceOrderShape(Request $request)
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
                DB::table('adv_order_shape')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('adv_order_shape')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function AdvanceOrderFoodType(Request $request)
    {
        $data = DB::table("adv_order_food_type")->get();
        return view("advance-order-food-type", compact("data"));
    }

    public function SaveAdvanceOrderFoodType(Request $request)
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
                DB::table('adv_order_food_type')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('adv_order_food_type')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function AdvanceOrderWeight(Request $request)
    {
        $data = DB::table("adv_order_weight")->get();
        return view("advance-order-weight", compact("data"));
    }

    public function SaveAdvanceOrderWeight(Request $request)
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
                DB::table('adv_order_weight')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('adv_order_weight')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function AdvanceOrderItems(Request $request)
    {

        $data = DB::table("adv_order_item_mst as a")
            ->select("a.*", "b.name as category")
            ->join("adv_order_category as b", "a.category_id", "b.id")
            ->get();
        $category = DB::table("adv_order_category")->get();
        $gst = DB::table("gst")->get();
        return view("advance-order-items", compact("data", "category", "gst"));
    }
    public function SaveAdvanceOrderItem(Request $request)
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

            $data = [
                "name" => $request->name,
                "category_id" => $request->category_id,
                "discount" => $request->discount,
                "margin" => $request->margin,
                "gst" => $request->gst,

            ];

            if (empty($request->id)) {
                $mst_id = DB::table('adv_order_item_mst')->insertGetId($data);
                $adv_order_flavour =  DB::table("adv_order_flavour")->get();
                foreach ($adv_order_flavour as $key => $value) {
                    DB::table("adv_order_item_det")->insert(array(
                        "mst_id" => $mst_id,
                        "flavour_id" => $value->id,
                    ));
                }
            } else {
                DB::table('adv_order_item_mst')->where("id", $request->id)->update($data);
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function GetFlavourDetails(Request $request)
    {
        return DB::table("adv_order_item_det as a")
            ->select("a.*", "b.name as flavour")
            ->join("adv_order_flavour as b", "a.flavour_id", "b.id")
            ->where("a.mst_id", $request->id)
            ->get();
    }

    public function UpdateAdvancedItem(Request $request)
    {
        try {
            DB::table("adv_order_item_det")->where("mst_id", $request->id)->update(array(
                "active" => 0
            ));
            if ($request->check) {
                foreach ($request->check as $key => $value) {
                    DB::table("adv_order_item_det")->where("id", $value)->update(array(
                        "active" => 1
                    ));
                }
            }

            foreach ($request->rate as $key => $value) {
                DB::table("adv_order_item_det")->where("id", $key)->update(array(
                    "fix_rate" => $value[0],
                    "increment_rate" => $value[1]
                ));
            }
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function CreateAdvanceOrder(Request $request)
    {
        $outlet =  DB::table('outlet')->get();
        $adv_order_item_mst =  DB::table('adv_order_item_mst')->get();
        $adv_order_weight =  DB::table('adv_order_weight')->get();
        $adv_order_shape =  DB::table('adv_order_shape')->get();
        $adv_order_food_type =  DB::table('adv_order_food_type')->get();
        return view("create-advance-order", compact("outlet", "adv_order_item_mst", "adv_order_weight", "adv_order_shape", "adv_order_food_type"));
    }

    public function GetFlavourDetailItem(Request $request)
    {
        return DB::table("adv_order_item_det as a")
            ->select("a.*", "b.name as flavour")
            ->join("adv_order_flavour as b", "a.flavour_id", "b.id")
            ->where("a.mst_id", $request->id)
            ->where("a.active", 1)
            ->get();
    }

    public function SaveAdvanceOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_date' => 'required',
            'delivery_date' => 'required',
            'outlet_id' => 'required',
            'customer_type' => 'required',
        ]);
        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('error', $firstError);
        }

        $inv_no =   DB::table("adv_order_mst")->whereDate("created_at", now())->count();
        if (!$inv_no) {
            $inv_no = 1;
        } else {
            $inv_no++;
        }
        $invoice_prefix =  DB::table("company_settings")->where("id", 1)->first();
        $invoice_id = $invoice_prefix->adv_order_prefix . date('d-m-y') . "-" . $inv_no;

        try {
            DB::beginTransaction();


            $customer_type_id = 0;
            if ($request->customer_type == "customer") {
                $customer_type_id = DB::table("customers")->select("customer_type_id")->where("id", $request->outlet_id)->first();
            } else {
                $customer_type_id =  DB::table("outlet")->select("customer_type_id")->where("id", $request->outlet_id)->first();
            }

            $customer_type_adv_item = DB::table("customer_type_adv_item")->where("customer_type_id", $customer_type_id->customer_type_id)->first();

            $prod_list = json_decode($request->prod_list);
            if (!$prod_list) {
                return redirect()->back()->with('error', "Select at least one product");
            }

            $gst_type = "";

            if ($request->order_type == "customer") {
                $city = DB::table("customer")->where("id", $request->outlet_id)->select("city")->first();
            } else {
                $city = DB::table("company_settings")->where("outlet_id", $request->outlet_id)->select("city")->first();
            }

            $company_setting = DB::table("company_settings")->where("id", 1)->first();
            if ($city->city && $company_setting->city) {
                if ($city->city == $company_setting->city) {
                    $gst_type = "Inner GST";
                } else {
                    $gst_type = "Outer GST";
                }
            } else {
                return redirect()->back()->with('error', "Select Outlet City");
            }




            $mst_id = DB::table("adv_order_mst")->insertGetId(array(
                "order_date" => $request->order_date,
                "delivery_date" => $request->delivery_date,
                "delivery_time" => $request->delivery_time,
                "outlet_id" => $request->outlet_id,
                "type" => $request->type,
                "user_id" => $request->user->id,
                "customer_type" => $request->customer_type,
                "order_id" => $invoice_id,
            ));

            $uploadedFiles = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = public_path('cake images');


                    $file->move($filePath, $filename);

                    $uploadedFiles[] = $filename;
                }
                $uploadedFilesString = implode(", ", $uploadedFiles);
            } else {
                $uploadedFilesString = "";
            }







            foreach ($prod_list as $key => $value) {

                $adv_order_item_det =  DB::table("adv_order_item_det")
                    ->where("id", $value->flavour_id)
                    ->first();

                $adv_order_item_mst = DB::table("adv_order_item_mst")->where("id", $value->product_id)->first();
                $total_price = 0;
                $customer_price = 0;
                $outlet_price = 0;
                if ($value->weight > 1) {
                    $extra_weight = ($value->weight - 1);
                    $increment_price = ($adv_order_item_det->increment_rate * $extra_weight);
                    $net_weight = $value->weight  - $extra_weight;
                    $price = $net_weight * $adv_order_item_det->fix_rate;
                    $total_price = ($increment_price + $price) * $value->qty;
                    $customer_price = $total_price;
                    $total_price = $total_price - ($total_price / 100 * $customer_type_adv_item->margin);
                    $outlet_price = $total_price;
                } else {
                    $total_price = ($adv_order_item_det->fix_rate * $value->weight) * $request->qty;
                    $customer_price = $total_price;
                    $total_price = $total_price - ($total_price / 100 * $customer_type_adv_item->margin);
                    $outlet_price = $total_price;
                }

                $total_price = $total_price - $value->discount_price;
                $customer_price = $customer_price - $value->discount_price;
                $outlet_price = $outlet_price - $value->discount_price;

                DB::table("adv_order_det")->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "flavour_id" => $adv_order_item_det->flavour_id,
                    "weight" => $value->weight,
                    "shape" => $value->shape,
                    "food_type" => $value->food_type,
                    "name" => $value->name,
                    "message" => $value->message,
                    "qty" => $value->qty,
                    "mrp" => $adv_order_item_det->fix_rate,
                    "increment_rate" => $adv_order_item_det->increment_rate,
                    "total_price" => $total_price,
                    "files" => $uploadedFilesString,
                    "description" => $value->description,
                    "discount_price" => $value->discount_price,
                    "customer_price" => $customer_price,
                    "outlet_price" => $outlet_price,
                    "gst" => $adv_order_item_mst->gst,
                    "gst_type" => $gst_type,

                ));
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function AdvanceOrderList(Request $request, $status)
    {
        if (request("printOrder")) {
            if (!request("ids")) {
                return redirect()->back()->with("error", "select at least one order");
            }

            $data = DB::table("adv_order_mst as a")
                ->select("a.*", "b.outlet_name as name")
                ->join("outlet as b", "a.outlet_id", "b.id")
                ->orderBy("a.id", "desc")
                ->whereIn("a.id", request("ids"))
                ->get();
            foreach ($data as $key => $value) {
                $data[$key]->details = DB::table("adv_order_det as a")
                    ->select("a.*", "b.name as flavour", "c.name as product")
                    ->join("adv_order_flavour as b", "a.flavour_id", "b.id")
                    ->join("adv_order_item_mst as c", "a.product_id", "c.id")
                    ->where("a.mst_id", $value->id)
                    ->get();
            }

            return view("advance-order-print", compact("data"));
        }
        $fromDt = request("date", date("Y-m-d"));
        $toDt = request("toDt", date("Y-m-d", strtotime("+5days")));


        if ($status == "invoices") {
            $outlet = DB::table("adv_order_mst as a")
                ->select("a.*", "b.outlet_name as name")
                ->join("outlet as b", "a.outlet_id", "b.id")
                ->whereDate("a.delivery_date", "=", $fromDt)
                ->where("a.customer_type", "outlet")
                ->where("a.is_invoice", 1);

            $customer = DB::table("adv_order_mst as a")
                ->select("a.*", "b.name as name")
                ->join("customers as b", "a.outlet_id", "b.id")
                ->whereDate("a.delivery_date", "=", $fromDt)
                ->where("a.customer_type", "customer")
                ->where("a.is_invoice", 1);
        } else {
            $outlet = DB::table("adv_order_mst as a")
                ->select("a.*", "b.outlet_name as name")
                ->join("outlet as b", "a.outlet_id", "b.id")
                ->whereDate("a.delivery_date", "=", $fromDt)
                ->where("a.customer_type", "outlet")
                ->where("a.status", $status);

            $customer = DB::table("adv_order_mst as a")
                ->select("a.*", "b.name as name")
                ->join("customers as b", "a.outlet_id", "b.id")
                ->whereDate("a.delivery_date", "=", $fromDt)
                ->where("a.customer_type", "customer")
                ->where("a.status", $status);
        }

        // Combine and order
        $data = DB::query()
            ->fromSub($outlet->union($customer), 'combined')
            ->orderByDesc('id')
            ->get();

        return view("advance-order-list", compact("data"));
    }

    public function AdvanceOrderView(Request $request, $id)
    {
        $outlet = DB::table("adv_order_mst as a")
            ->select("a.*", "b.outlet_name as name")
            ->join("outlet as b", "a.outlet_id", "b.id")
            ->orderBy("a.id", "desc")
            ->where("a.id", $id)
            ->where("a.customer_type", "outlet")
            ->first();

        $customer = DB::table("adv_order_mst as a")
            ->select("a.*", "b.name as name")
            ->join("customers as b", "a.outlet_id", "b.id")
            ->orderBy("a.id", "desc")
            ->where("a.id", $id)
            ->where("a.customer_type", "customer")
            ->first();

        $order_det = DB::table("adv_order_det as a")
            ->select("a.*", "b.name as flavour", "c.name as product")
            ->join("adv_order_flavour as b", "a.flavour_id", "b.id")
            ->join("adv_order_item_mst as c", "a.product_id", "c.id")
            ->where("a.mst_id", $id)
            ->get();
        $order_mst = $outlet ?? $customer;

        return view("advance-order-view", compact("order_mst", "order_det"));
    }

    public function GetCustomerOrOutlet(Request $request)
    {
        if ($request->type == "customer") {
            return DB::table("customers")->get();
        } else {
            return DB::table("outlet")->select("outlet_name as name", "id")->get();
        }
    }

    public function UpdateStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',

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
            $invoice_id = getInvoiceNo();
           DB::table("adv_order_mst")->where("id", $request->id)->update(array(
                // "is_invoice" => 1,
                "status" => $request->status,
                "order_id" => $invoice_id,

            ));
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function customerTypeAdvanceItems(Request $request, $id)
    {
        $category_id = request("category_id");

        $customer_type_prod = DB::table("customer_type_adv_item as a")
            ->select("a.*", "b.name", "c.name as category")
            ->join("adv_order_item_mst as b", "a.adv_item_id", "b.id")
            ->join("adv_order_category as c", "b.category_id", "c.id")

            ->where("a.customer_type_id", $id);
        if ($category_id > 0) {
            $customer_type_prod->where("b.category_id", $category_id);
        }
        $customer_type_product = $customer_type_prod->get();




        $customer_type = DB::table("customer_type")->where("id", $id)->first();

        $products = DB::table("finish_products_mst")->get();


        $products = DB::table("adv_order_item_mst as a")
            ->select("a.*", "b.name as category")
            ->join("adv_order_category as b", "a.category_id", "b.id")
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from("customer_type_adv_item as c")
                    ->whereColumn("a.id", "c.adv_item_id")
                    ->where("c.customer_type_id", $id);
            })
            ->get();

        $sub_category = DB::table("f_product_sub_category")->get();


        return view("customer-type-advance-items", compact("customer_type_product", "customer_type", "products", "sub_category"));
    }


    public function AllocateAdvanceItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_type_id' => 'required',
            'product_id' => 'required',

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

        foreach ($request->product_id as $key => $value) {


            $customer_type_adv_item = DB::table("customer_type_adv_item")
                ->where("customer_type_id", $request->customer_type_id)
                ->where("adv_item_id", $value)

                ->first();
            if (!$customer_type_adv_item) {
                DB::table('customer_type_adv_item')->insertGetId(array(

                    "customer_type_id" => $request->customer_type_id,
                    "adv_item_id" => $value,


                ));
            }
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function UpdateAdvanceItem(Request $request)
    {
        $validator = Validator::make($request->all(), [


            'margin' => 'required',

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

            foreach ($request->margin as $key => $value) {
                DB::table('customer_type_adv_item')->where("id", $key)->update(array(

                    "margin" => $value[0],



                ));
            }
        } catch (Exception $e) {


            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function GetAdvProduct(Request $request)
    {
        $customer_type_id = 0;
        if ($request->customer_type == "customer") {
            $customer_type_id = DB::table("customers")->select("customer_type_id")->where("id", $request->id)->first();
        } else {
            $customer_type_id =  DB::table("outlet")->select("customer_type_id")->where("id", $request->id)->first();
        }

        return DB::table("customer_type_adv_item as a")->select("b.id", "b.name", "a.margin")
            ->join("adv_order_item_mst as b", "a.adv_item_id", "b.id")
            ->where("a.customer_type_id", $customer_type_id->customer_type_id)->get();
    }

    public function Cancel_Order(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'order_pwd' => 'required',
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

            $company_setting = DB::table("company_settings")->where("id", $request->user->id)->select('order_pwd')->first();
            if ($request->order_pwd !== $company_setting->order_pwd) {
                return  redirect()->back()->with("error", 'Incorrect password.');
            }

            DB::table('adv_order_mst')->where("id", $request->id)->update(array(
                "status" => "cancel",

            ));
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }

        return  redirect()->back()->with("success", "Cancel Successfully");
    }

    public function advConvertToInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'convertID' => 'required',

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


            DB::table('adv_order_mst')->where("id", $request->convertID)->update(array(
                "is_invoice" => 1,
                "status" => "delivered",

            ));
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }

        return  redirect()->back()->with("success", "Cancel Successfully");
    }


    public function advanceInvoiceView(Request $request, $id)
    {

        $outlet = DB::table("adv_order_mst as a")
            ->select("a.*", "b.outlet_name as name", "b.number", "b.address", "b.city", "b.state")
            ->join("outlet as b", "a.outlet_id", "b.id")
            ->orderBy("a.id", "desc")
            ->where("a.id", $id)
            ->where("a.customer_type", "outlet")
            ->first();

        $customer = DB::table("adv_order_mst as a")
            ->select("a.*", "b.name as name", "b.number", "b.address", "b.city", "b.state")
            ->join("customers as b", "a.outlet_id", "b.id")
            ->orderBy("a.id", "desc")
            ->where("a.id", $id)
            ->where("a.customer_type", "customer")
            ->first();

        $order_det = DB::table("adv_order_det as a")
            ->select("a.*", "a.outlet_price as price", "b.name as flavour", "c.name as product", DB::raw("'Inner Gst' as gst_type,'0' as cess_amt"))
            ->join("adv_order_flavour as b", "a.flavour_id", "b.id")
            ->join("adv_order_item_mst as c", "a.product_id", "c.id")
            ->where("a.mst_id", $id)
            ->get();
        $order_mst = $outlet ?? $customer;

        return view("advanced-invoice-view", compact("order_mst", "order_det"));
    }
}
