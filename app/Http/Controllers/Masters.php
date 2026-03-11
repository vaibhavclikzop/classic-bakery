<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Carbon\Carbon;

use Illuminate\Support\Str;

class Masters extends Controller
{


    function generateRandomNumber($length = 12)
    {
        $number = '';
        while (strlen($number) < $length) {
            $number .= mt_rand(0, 9);
        }
        return substr($number, 0, $length);
    }

    public function GetCity(Request $request)
    {
        $state_city = DB::table("state_city")->distinct("state")->where("state", $request->state)->get();;
        return $state_city;
    }


    public function GetCategory(Request $request)
    {
        $category = DB::table("category")->where("brand_id", $request->id)->get();;
        return $category;
    }


    public function GetSubCategory(Request $request)
    {
        $sub_category = DB::table("sub_category")->where("category_id", $request->id)->get();;
        return $sub_category;
    }
    public function GetFinishSubCategory(Request $request)
    {
        $sub_category = DB::table("f_product_sub_category")->where("f_category_id", $request->id)->get();;
        return $sub_category;
    }

    public function GetProducts(Request $request)
    {
        $products = DB::table("products")->where("sub_category_id", $request->id)->get();;
        return $products;
    }

    public function GetProductFinish(Request $request)
    {
        return DB::table("finish_products_mst")->where("f_sub_category_id", $request->id)->get();
    }


    public function GetUserDetails(Request $request)
    {
        $user = DB::table("users")->where("id", $request->id)->first();;
        return $user;
    }

    public function GetTeamMember(Request $request)
    {
        $teams = [];
        $team = DB::table("team")->where("id", $request->id)->first();

        if ($team) {
            $team_members = DB::table("team_member")->where("mst_id", $request->id)->get();
            $teams[$team->name] = $team_members;
        }
        return $teams;
    }

    public function Company(Request $request)
    {
        $company = DB::table("company")->get();
        return view("company", compact('company'));
    }

    public function SaveCompany(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_name' => 'required',

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
                DB::table('company')->insertGetId(array(
                    "name" => $request->company_name,
                    "business_type" => $request->business_type,
                    "source" => $request->source,
                    "ref_name" => $request->reference,
                    "number" => $request->number,
                    "remarks" => $request->remarks,
                ));
            } else {
                DB::table('company')->where("id", $request->id)->update(array(
                    "name" => $request->company_name,
                    "business_type" => $request->business_type,
                    "source" => $request->source,
                    "ref_name" => $request->reference,
                    "number" => $request->number,
                    "remarks" => $request->remarks,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function Customers(Request $request)
    {
        $customers = DB::table("customers as a")
            ->select("a.*", "b.name as customer_type")
            ->join("customer_type as b", "a.customer_type_id", "b.id")
            ->get();
        $customer_type = DB::table("customer_type")->get();

        return view("customers", compact('customers', 'customer_type'));
    }


    public function SaveCustomer(Request $request)
    {



        $validator = Validator::make($request->all(), [

            'number' => 'required',
            'name' => 'required',
            'customer_type_id' => 'required',

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
        $customer_id = 0;
        try {
            if (empty($request->id)) {
                $customer_id =  DB::table('customers')->insertGetId(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "customer_type_id" => $request->customer_type_id,
                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_city" => $request->ship_city,
                    "ship_pincode" => $request->ship_pincode,
                    "fssai_no" => $request->fssai_no,
                    "ship_fssai_no" => $request->ship_fssai_no,
                    "ship_gst" => $request->ship_gst,
                    "nickname" => $request->nickname,
                ));
            } else {
                DB::table('customers')->where("id", $request->id)->update(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "customer_type_id" => $request->customer_type_id,
                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_city" => $request->ship_city,
                    "ship_pincode" => $request->ship_pincode,
                    "fssai_no" => $request->fssai_no,
                    "ship_fssai_no" => $request->ship_fssai_no,
                    "ship_gst" => $request->ship_gst,
                    "nickname" => $request->nickname,
                ));
                $customer_id = $request->id;
            }

            // $customer_type_product =  DB::table("customer_type_product")->where("customer_type_id", $request->customer_type_id)->get();
            // foreach ($customer_type_product as $key => $value) {
            //     DB::table('customer_products')->insertGetId(array(
            //         "customer_id" => $customer_id,
            //         "finish_product_id" => $value->finish_product_id,
            //         "price" => $value->price,
            //         "sale_price" => $value->sale_price,

            //     ));
            // }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function VendorType(Request $request)
    {

        $vendor_type = DB::table("vendor_type")->get();
        return view("vendor-type", compact('vendor_type'));
    }


    public function SaveVendorType(Request $request)
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
                DB::table('vendor_type')->insertGetId(array(

                    "name" => $request->name,

                ));
            } else {
                DB::table('vendor_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Vendor(Request $request)
    {

        $vendor = DB::table("vendor as a")
            ->select("a.*", "a.company_name as company")

            ->get();
        $company = DB::table("company")->get();
        return view("vendor", compact('vendor', "company"));
    }


    public function SaveVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'number' => 'required',
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
                DB::table('vendor')->insertGetId(array(
                    "company_name" => $request->company_name,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    // "company_id"=>
                ));
            } else {
                DB::table('vendor')->where("id", $request->id)->update(array(
                    "company_name" => $request->company_name,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function StoreLocation(Request $request)
    {

        $store = DB::table("store")->get();
        return view("store-location", compact('store'));
    }


    public function SaveStoreLocation(Request $request)
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
                DB::table('store')->insertGetId(array(

                    "name" => $request->name,
                    "address" => $request->address,

                ));
            } else {
                DB::table('store')->where("id", $request->id)->update(array(

                    "name" => $request->name,
                    "address" => $request->address,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function UnitType(Request $request)
    {

        $unit_type = DB::table("unit_type")->get();
        return view("unit-type", compact('unit_type'));
    }

    public function SaveUnitType(Request $request)
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
                DB::table('unit_type')->insertGetId(array(

                    "name" => $request->name,


                ));
            } else {
                DB::table('unit_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Brand(Request $request)
    {

        $brand = DB::table("brand")->get();
        return view("brand", compact('brand'));
    }

    public function SaveBrand(Request $request)
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
                DB::table('brand')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('brand')->where("id", $request->id)->update(array(
                    "name" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Category(Request $request)
    {

        $category = DB::table("category")->get();
        $brand = DB::table("brand")->get();
        return view("category", compact('category', "brand"));
    }

    public function SaveCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'brand_id' => 'required',

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
                DB::table('category')->insertGetId(array(
                    "name" => $request->name,
                    "brand_id" => $request->brand_id,
                ));
            } else {
                DB::table('category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "brand_id" => $request->brand_id,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function SubCategory(Request $request)
    {

        $sub_category = DB::table("sub_category as a")
            ->select("a.*", "b.name as category_name", "c.id as brand_id")
            ->join("category as b", "a.category_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id")

            ->get();
        $brand = DB::table("brand")->get();
        return view("sub-category", compact('sub_category', "brand"));
    }



    public function SaveSubCategory(Request $request)
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
            if (empty($request->id)) {
                DB::table('sub_category')->insertGetId(array(
                    "name" => $request->name,
                    "category_id" => $request->category_id,
                ));
            } else {
                DB::table('sub_category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "category_id" => $request->category_id,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Product(Request $request)
    {

        $category_id = $request->input("category_id");
        $sub_category_id = $request->input("sub_category_id");
        $search = $request->input('search');
        $perPage = $request->input('perPage', 0);

        $product = DB::table("products as a")
            ->select("a.*", "b.name as category_name", "c.name as brand_name", "ut.name as unit_type", "d.name as sub_category")
            ->join("category as b", "a.category_id", "b.id")
            ->join("brand as c", "a.brand_id", "c.id")
            ->join("sub_category as d", "a.sub_category_id", "d.id")
            ->leftJoin("unit_type as ut", "ut.id", "a.uom");

        if ($category_id) {
            $product->where("a.category_id", "=", $category_id);
        }
        if ($sub_category_id) {
            $product->where("a.sub_category_id", "=", $sub_category_id);
        }

        if (!empty($search)) {
            $product->where(function ($q) use ($search) {
                $q->where('a.name', 'like', "%{$search}%")
                    ->orWhere('a.article_no', 'like', "%{$search}%")
                    ->orWhere('c.name', 'like', "%{$search}%")
                    ->orWhere('b.name', 'like', "%{$search}%");
            });
        }
        if ($perPage > 0) {
            $products = $product->paginate($perPage);
        } else {
            $perPage = PHP_INT_MAX;
            $products = $product->paginate($perPage);
        }


        $products->appends(['search' => $search, 'perPage' => $perPage]);
        $brand = DB::table("brand")->get();
        $unit_type = DB::table("unit_type")->get();
        $gst = DB::table("gst")->get();
        $product_category = DB::table("category")->get();

        $sub_category = collect();
        if ($category_id) {
            $sub_category = DB::table("sub_category")->where("category_id", $category_id)->get();
        }

        return view("products", compact('products', "brand", "unit_type", "gst", "product_category", "sub_category"));
    }

    public function SaveProduct(Request $request)
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
        $barcode = $this->generateRandomNumber(10);
        $raw_material = 0;
        if (!empty($request->raw_material)) {
            $raw_material = implode(', ', $request->raw_material);
        }

        $file = "";
        if ($request->hasFile('file')) {
            $file = time() . '.' . $request->file('file')->extension();
            $request->file('file')->move('product images', $file);
        } else {
            if (!empty($request->id)) {
                $products = DB::table("products")->where("id", $request->id)->first();
                $file = $products->image;
            }
        }
        try {


            $category =  DB::table("category")->where("id", $request->category_id)->first();
            $sub_category =  DB::table("sub_category")->where("id", $request->sub_category_id)->first();
            $article_no = date("ymdHis") . strtoupper(substr($category->name, 0, 1)) . strtoupper(substr($sub_category->name, 0, 1)) . strtoupper(substr($request->name, 0, 1)) .
                strtoupper(Str::random(3));

            if (empty($request->id)) {
                $id = DB::table('products')->insertGetId(array(
                    "brand_id" => $request->brand_id,
                    "category_id" => $request->category_id,
                    "sub_category_id" => $request->sub_category_id,

                    "name" => $request->name,
                    "article_no" => $article_no,

                    "price" => $request->price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,
                    "warranty_days" => $request->warranty_days,
                    "active" => $request->active,
                    "bar_code" => $barcode,
                    "raw_material" => $raw_material,
                    "gst" => $request->gst,
                    "image" => $file,
                    "manual_barcode" => $request->manual_barcode,
                    "cess_tax" => $request->cess_tax,
                    "hindi" => $request->hindi

                ));
            } else {
                DB::table('products')->where("id", $request->id)->update(array(
                    "brand_id" => $request->brand_id,
                    "category_id" => $request->category_id,
                    "sub_category_id" => $request->sub_category_id,
                    "name" => $request->name,

                    "price" => $request->price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,
                    "warranty_days" => $request->warranty_days,

                    "active" => $request->active,
                    "raw_material" => $raw_material,
                    "gst" => $request->gst,
                    "image" => $file,
                    "manual_barcode" => $request->manual_barcode,
                    "cess_tax" => $request->cess_tax,
                    "hindi" => $request->hindi

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }




    public function VendorProduct(Request $request, $id)
    {
        $vendor_product = DB::table("products as a")
            ->select("a.*", "c.name as brand", "d.name as category", "e.price", "e.id", "e.active")
            ->join("brand as c", "a.brand_id", "c.id")
            ->join("category as d", "a.category_id", "d.id")
            ->join("vendor_product as e", "a.id", "e.product_id")
            ->where("e.vendor_id", $id)
            ->get();

        $vendor = DB::table("vendor")->where("id", $id)->first();
        $products = DB::table("products as d")
            ->select("d.*", "b.name as brand", "c.name as category", "e.name as sub_category")
            ->leftJoin("brand as b", "d.brand_id", "b.id")
            ->leftJoin("category as c", "d.category_id", "c.id")
            ->leftJoin("sub_category as e", "d.sub_category_id", "e.id")

            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from("vendor_product as a")
                    ->whereColumn("a.product_id", "d.id")
                    ->where("a.vendor_id", $id);
            })
            ->get();



        return view("vendor-product", compact("vendor_product", "vendor", "products"));
    }



    public function UpdateVendorPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'price' => 'required',

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

            foreach ($request->price as $key => $value) {
                DB::table("vendor_product")->where("id", $key)->update(array("price" => $value[0]));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }




    public function AllocateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
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


            DB::table('vendor_product')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "product_id" => $value,
            ));
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function Settings(Request $request)
    {
        $settings = DB::table("company_settings")->where("id", 1)->first();
        $stock = DB::table("current_stock")->count();

        $city = DB::table("state_city")->orderBy("city", "asc")->get();
        $permission_mst = DB::table("permission_mst")
            ->orderBy("id", "asc")
            ->get();
        return view("settings", compact("settings", "stock", "permission_mst", "city"));
    }

    public function SaveSettings(Request $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = time() . '.' . $request->image->extension();

            $request->image->move('logo', $image);
        } else {
            $company_settings = DB::table("company_settings")->where("id", 1)->first();
            $image = $company_settings->img;
        }
        $stock = DB::table("current_stock")->count();
        if ($stock < $request->audit_count) {
            return  redirect()->back()->with("error", "Audit report can not be more then cs products");
        }
        DB::table('company_settings')->where("id", 1)->update(array(
            "img" => $image,
            "img_width" => $request->img_width,
            "company_name" => $request->company_name,
            "address" => $request->address,
            "contact_person" => $request->contact_person,
            "number" => $request->number,
            "email" => $request->email,
            "gst_no" => $request->gst_no,
            "invoice_prefix" => $request->invoice_prefix,

            "fssai_no" => $request->fssai_no,
            "pan_no" => $request->pan_no,
            "cin_no" => $request->cin_no,
            "city" => $request->city,
            "order_pwd" => $request->order_pwd,
            "order_prefix" => $request->order_prefix,
            "adv_order_prefix" => $request->adv_order_prefix,
            "po_prefix" => $request->po_prefix,
            "create_order_prefix" => $request->create_order_prefix,
            "outward_production_prefix" => $request->outward_production_prefix,


        ));

        return  redirect()->back()->with("success", "Save Successfully");
    }



    public function SaveHeaderMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

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



            DB::table('permission_mst')->where("id", $request->id)->update(array(


                "name" => $request->name,

            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function FinishProduct(Request $request)
    {

        $f_category_id = $request->input("f_category_id");
        $f_sub_category_id = $request->input("f_sub_category_id");
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $product = DB::table("finish_products_mst as a")
            ->select(
                "a.*",
                "b.name as category_name",
                "ut.name as unit_type",
                "c.name as sub_category"
            )
            ->join("f_product_category as b", "a.f_category_id", "b.id")
            ->join("f_product_sub_category as c", "a.f_sub_category_id", "c.id")

            ->leftJoin("unit_type as ut", "ut.id", "a.uom");
        if ($f_category_id) {
            $product->where("a.f_category_id", "=", $f_category_id);
        }
        if ($f_sub_category_id) {
            $product->where("a.f_sub_category_id", "=", $f_sub_category_id);
        }

        if (!empty($search)) {
            $product->where(function ($q) use ($search) {
                $q->where('a.name', 'like', "%{$search}%")
                    ->orWhere('a.article_no', 'like', "%{$search}%")
                    ->orWhere('b.name', 'like', "%{$search}%");
            });
        }


        if ($perPage > 0) {
            $products = $product->paginate($perPage);
        } else {
            $perPage = PHP_INT_MAX;
            $products = $product->paginate($perPage);
        }



        $products->appends(['search' => $search, 'perPage' => $perPage]);


        $f_product_category = DB::table("f_product_category")->get();

        $sub_category = collect();
        if ($f_category_id) {
            $sub_category = DB::table("f_product_sub_category")->where("f_category_id", $f_category_id)->get();
        }
        $unit_type = DB::table("unit_type")->get();
        $gst = DB::table("gst")->get();
        $brand = DB::table("brand")->get();
        return view("finish-products", compact('products', "f_product_category", "unit_type", "gst", "brand", "sub_category"));
    }


    public function RawMaterialProduct(Request $request, $id)
    {
        $products = DB::table("products as a")
            ->select("a.*", "b.name as category_name", "c.name as brand_name", "ut.name as unit_type", "fp.qty", "fp.id as eID")
            ->join("category as b", "a.category_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id")

            ->LeftJoin("unit_type as ut", "ut.id", "a.uom")
            ->join("finish_products_det as fp", "a.id", "fp.product_id")
            ->where("fp.mst_id", $id)

            ->get();
        $mst_id = $id;
        $brand = DB::table("brand")->get();
        return view("raw-material-product", compact("products", "brand", "mst_id"));
    }

    public function SaveRawProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'qty' => 'required',


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
            if (!empty($request->id)) {


                DB::table('finish_products_det')->where("id", $request->id)->update(array(

                    "qty" => $request->qty,

                ));
            } else {

                $finish_products_det = DB::table("finish_products_det")->where("mst_id", $request->mst_id)->where("product_id", $request->product_id)->first();
                if ($finish_products_det) {
                    return  redirect()->back()->with("error", "Raw material already added");
                } else {

                    DB::table('finish_products_det')->insertGetId(array(

                        "mst_id" => $request->mst_id,
                        "qty" => $request->qty,
                        "product_id" => $request->product_id,

                    ));
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function DeleteProduct(Request $request)
    {

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
        try {
            DB::table('finish_products_det')->where("id", $request->id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function Users(Request $request)
    {


        $users = DB::table("users as a")
            ->select("a.*", "b.name as user_type")
            ->join("role as b", "a.role_id", "b.id")
            ->where("user_type", "!=", "admin")
            ->whereIn("a.id", $request->userIds)
            ->get();


        $role = DB::table("role")->where("name", "!=", "admin")->get();
        $department = DB::table("department")->get();
        return view("users", compact("users", "role", "department"));
    }
    public function SaveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'email' => 'required',
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


        try {
            if (empty($request->id)) {
                DB::table('users')->insertGetId(array(
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "role_id" => $request->role_id,
                    "password" => $request->password,
                    "parent_id" => 1,
                    "department_id" => $request->department_id,


                ));
            } else {
                DB::table('users')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "role_id" => $request->role_id,
                    "password" => $request->password,
                    "parent_id" => 1,
                    "department_id" => $request->department_id,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }



    public function UserRole(Request $request)
    {
        $role = DB::table("role")->where("name", "!=", "admin")->get();
        // $role = DB::table("role")->get();
        return view("user-role", compact("role"));
    }

    public function SaveRole(Request $request)
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
                DB::table('role')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('role')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function UserPermission(Request $request, $id)
    {

        $role = DB::table("role")->where("id", $id)->first();


        $permission_mst = DB::table("permission_mst as a")
            ->select("a.*")
            ->whereNotExists(function ($query) use ($role) {
                $query->select(DB::raw(1))
                    ->from("role_permission as b")
                    ->whereColumn("b.permission_id", "a.id")
                    ->where("b.role_id", $role->id);
            })
            ->get();



        $role_permission = DB::table("role_permission as a")
            ->select("a.*", "b.name as permission")
            ->join("permission_mst as b", "a.permission_id", "b.id")
            ->where("a.role_id", $role->id)
            ->get();

        return view("user-permission", compact("role", "permission_mst", "role_permission", "id"));
    }

    public function SaveUserPermission(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
            'permission_id' => 'required',
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

        $role_permission = DB::table("role_permission")->where("role_id", $request->role_id)->where("permission_id", $request->permission_id)->first();
        if ($role_permission) {
            return  redirect()->back()->with("error", "User permission already added");
        }
        try {
            if (empty($request->id)) {
                DB::table('role_permission')->insertGetId(array(
                    "role_id" => $request->role_id,
                    "permission_id" => $request->permission_id,
                    "edit" => $request->edit,
                    "view" => $request->view,
                ));
            } else {
                DB::table('role_permission')->where("id", $request->id)->update(array(

                    "edit" => $request->edit,
                    "view" => $request->view,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function RemovePermission(Request $request)
    {

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

        DB::table('role_permission')->where("id", $request->id)->delete();
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function UpdateGenSet(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'hsn_code' => 'required|min:4',

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

        $file = "";
        if ($request->hasFile('file')) {
            $file = time() . '.' . $request->file('file')->extension();
            $request->file('file')->move('product images', $file);
        } else {
            if (!empty($request->id)) {
                $products = DB::table("finish_products_mst")->where("id", $request->id)->first();
                $file = $products->image;
            }
        }

        try {

            DB::table('finish_products_mst')->where("id", $request->id)->update(array(
                "gst" => $request->gst,
                "image" => $file,
                "f_category_id" => $request->category_id,
                "f_sub_category_id" => $request->sub_category_id,
                "name" => $request->name,
                "article_no" => $request->article_no,
                "price" => $request->price,
                "min_stock" => $request->minimum_stock,
                "uom" => $request->uom,
                "active" => $request->active,
                "image" => $file,
                "cess_tax" => $request->cess_tax,
                "hsn_code" => $request->hsn_code,
                "manual_barcode" => $request->manual_barcode,
                "warranty_days" => $request->warranty_days

            ));



            DB::table("customer_type_product")
                ->where("finish_product_id", $request->id)
                ->update([
                    "price"      => $request->price,
                    "sale_price" => DB::raw("{$request->price} - ({$request->price}/100 * margin)")
                ]);
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Gst(Request $request)
    {

        $gst =  DB::table("gst")->get();
        return view("gst", compact("gst"));
    }

    public function SaveGst(Request $request)
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
                DB::table('gst')->insertGetId(array(
                    "gst" => $request->name,



                ));
            } else {
                DB::table('gst')->where("id", $request->id)->update(array(
                    "gst" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function CustomerType(Request $request)
    {
        $customer_type =  DB::table("customer_type")->get();
        return view("customer-type", compact("customer_type"));
    }

    public function SaveCustomerType(Request $request)
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
                DB::table('customer_type')->insertGetId(array(

                    "name" => $request->name,

                ));
            } else {
                DB::table('customer_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Outlet(Request $request)
    {
        $outlet =  DB::table("outlet")->get();
        $customer_type =  DB::table("customer_type")->get();
        return view("outlet", compact("outlet", "customer_type"));
    }


    public function OutletProduct(Request $request, $id)
    {
        $outlet_product = DB::table("outlet as a")
            ->select("a.*", "c.name", "b.price", "b.sale_price")
            ->join("customer_type_product as b", "a.customer_type_id", "b.customer_type_id")
            ->join("finish_products_mst as c", "b.finish_product_id", "c.id")
            ->where("a.id", $id)
            ->get();

        $outlet = DB::table("outlet")->where("id", $id)->first();

        return view("outlet-product", compact("outlet_product", "outlet"));
    }

    public function AllocateOutletProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required',
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

            $finish_products_mst = DB::table("finish_products_mst")->where("id", $value)->first();
            $outlet_product = DB::table("outlet_product")
                ->where("outlet_id", $request->outlet_id)
                ->where("finish_product_id", $value)

                ->first();
            if (!$outlet_product) {
                DB::table('outlet_product')->insertGetId(array(

                    "outlet_id" => $request->outlet_id,
                    "finish_product_id" => $value,
                    "price" => $finish_products_mst->price,

                ));
            }
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function CustomerTypeProduct(Request $request, $id)
    {
        $sub_category_id = request("sub_category_id");

        $customer_type_prod = DB::table("customer_type_product as a")
            ->select("a.*", "b.name", "c.name as category", "d.name as sub_category", "b.article_no")
            ->join("finish_products_mst as b", "a.finish_product_id", "b.id")
            ->join("f_product_category as c", "b.f_category_id", "c.id")
            ->join("f_product_sub_category as d", "b.f_sub_category_id", "d.id")
            ->where("a.customer_type_id", $id);
        if ($sub_category_id > 0) {
            $customer_type_prod->where("b.f_sub_category_id", $sub_category_id);
        }
        $customer_type_product = $customer_type_prod->get();
        $customer_type = DB::table("customer_type")->where("id", $id)->first();

        $products = DB::table("finish_products_mst")->get();

        $products = DB::table("finish_products_mst as a")
            ->select("a.*", "b.name as category", "c.name as sub_category")

            ->leftJoin("f_product_category as b", "a.f_category_id", "b.id")
            ->join("f_product_sub_category as c", "a.f_sub_category_id", "c.id")
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from("customer_type_product as c")
                    ->whereColumn("a.id", "c.finish_product_id")
                    ->where("c.customer_type_id", $id);
            })
            ->get();

        $sub_category = DB::table("f_product_sub_category")->get();




        return view("customer-type-product", compact("customer_type_product", "customer_type", "products", "sub_category"));
    }

    public function AllocateCustomerTypeProduct(Request $request)
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

            $finish_products_mst = DB::table("finish_products_mst")->where("id", $value)->first();
            $outlet_product = DB::table("customer_type_product")
                ->where("customer_type_id", $request->customer_type_id)
                ->where("finish_product_id", $value)

                ->first();
            if (!$outlet_product) {
                DB::table('customer_type_product')->insertGetId(array(

                    "customer_type_id" => $request->customer_type_id,
                    "finish_product_id" => $value,
                    "price" => $finish_products_mst->price,
                    "sale_price" => $finish_products_mst->price,

                ));
            }
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function CustomerProducts(Request $request, $id)
    {
        $customer_products = DB::table("customers as a")
            ->select("a.*", "c.name", "b.price", "b.sale_price")
            ->join("customer_type_product as b", "a.customer_type_id", "b.customer_type_id")
            ->join("finish_products_mst as c", "b.finish_product_id", "c.id")
            ->where("a.id", $id)
            ->get();

        $customer = DB::table("customers")->where("id", $id)->first();
        return view("customer-products", compact("customer_products", "customer"));
    }

    public function UpdateAllMargin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_type_id' => 'required',
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


            $update = DB::table('customer_type_product as a')
                ->join("finish_products_mst as b", "a.finish_product_id", "=", "b.id")
                ->where("a.customer_type_id", $request->customer_type_id);
            if ($request->sub_category_id > 0) {
                $update->where("b.f_sub_category_id", $request->sub_category_id);
            }
            $update->update([
                "margin" => $request->margin,
                "sale_price" => DB::raw("a.price-(a.price / 100 * $request->margin)"),
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function UpdateCustomerTypePrice(Request $request)
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
                DB::table('customer_type_product')->where("id", $key)->update(array(

                    "margin" => $value[0],
                    "sale_price" => DB::raw("price-(price / 100 * $value[0])"),


                ));
            }
        } catch (Exception $e) {


            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function FinishProductCategory(Request $request)
    {

        $category = DB::table("f_product_category")->get();

        return view("finish-product-category", compact('category'));
    }

    public function SaveFinishProductCategory(Request $request)
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
                DB::table('f_product_category')->insertGetId(array(
                    "name" => $request->name,


                ));
            } else {
                DB::table('f_product_category')->where("id", $request->id)->update(array(

                    "name" => $request->name,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function FinishProductSubCategory(Request $request)
    {

        $sub_category = DB::table("f_product_sub_category as a")
            ->select("a.*", "b.name as category_name")
            ->join("f_product_category as b", "a.f_category_id", "b.id")


            ->get();
        $category = DB::table("f_product_category")->get();
        $order_type = DB::table("order_type")->get();
        return view("finish-product-sub-category", compact('category', "sub_category", "order_type"));
    }



    public function SaveFinishProductSubCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'f_category_id' => 'required',


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
                DB::table('f_product_sub_category')->insertGetId(array(
                    "name" => $request->name,
                    "f_category_id" => $request->f_category_id,

                ));
            } else {
                DB::table('f_product_sub_category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "f_category_id" => $request->f_category_id,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function SaveFinishProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'category_id' => 'required',
            'hsn_code' => 'required|digits:4',

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
        $barcode = $this->generateRandomNumber(10);
        $raw_material = 0;
        if (!empty($request->raw_material)) {
            $raw_material = implode(', ', $request->raw_material);
        }

        $file = "";
        if ($request->hasFile('file')) {
            $file = time() . '.' . $request->file('file')->extension();
            $request->file('file')->move('product images', $file);
        } else {
            if (!empty($request->id)) {
                $products = DB::table("products")->where("id", $request->id)->first();
                $file = $products->image;
            }
        }
        try {


            $category =  DB::table("f_product_category")->where("id", $request->category_id)->first();
            $sub_category =  DB::table("f_product_sub_category")->where("id", $request->sub_category_id)->first();
            $article_no = date("ymdHis") . strtoupper(substr($category->name, 0, 1)) . strtoupper(substr($sub_category->name, 0, 1)) . strtoupper(substr($request->name, 0, 1)) .
                strtoupper(Str::random(3));



            if (empty($request->id)) {
                $mst_id = DB::table('finish_products_mst')->insertGetId(array(

                    "f_category_id" => $request->category_id,
                    "f_sub_category_id" => $request->sub_category_id,

                    "name" => $request->name,
                    "article_no" => $article_no,
                    "price" => $request->price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,
                    "gst" => $request->gst,


                    "active" => $request->active,
                    "bar_code" => $barcode,

                    "image" => $file,
                    "cess_tax" => $request->cess_tax,
                    "hsn_code" => $request->hsn_code,
                    "manual_barcode" => $request->manual_barcode

                ));
            } else {
                DB::table('finish_products_mst')->where("id", $request->id)->update(array(

                    "category_id" => $request->category_id,
                    "sub_category_id" => $request->sub_category_id,
                    "name" => $request->name,
                    "article_no" => $request->article_no,
                    "price" => $request->price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,


                    "active" => $request->active,

                    "image" => $file,
                    "cess_tax" => $request->cess_tax,
                    "hsn_code" => $request->hsn_code,

                ));
                $mst_id = $request->id;
            }
            $prod_list = json_decode($request->prod_List);
            foreach ($prod_list as $key => $value) {

                DB::table('finish_products_det')->insertGetId(array(
                    "mst_id" => $mst_id,

                    "qty" => $value->qty,
                    "product_id" => $value->product_id,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Department(Request $request)
    {
        $department =  DB::table("department")->get();
        return view("department", compact("department"));
    }

    public function SaveDepartment(Request $request)
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
                DB::table('department')->insertGetId(array(
                    "name" => $request->name,
                    "contact_person" => $request->contact_person,
                    "number" => $request->number,
                ));
            } else {
                DB::table('department')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "contact_person" => $request->contact_person,
                    "number" => $request->number,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function DepartmentProduct(Request $request, $id)
    {
        $department_product = DB::table("finish_products_mst as a")
            ->select("a.*", "b.name as category")
            ->join("f_product_sub_category as b", "a.f_sub_category_id", "b.id")
            ->where("a.department_id", $id)
            ->get();
        $department = DB::table("department")->where("id", $id)->first();

        $category = DB::table("finish_products_mst as a")
            ->select("a.f_sub_category_id as id", "b.name")
            ->join("f_product_sub_category as b", "a.f_sub_category_id", "b.id")
            ->distinct("a.f_sub_category_id")
            ->where("a.department_id", 0)->get();

        $allocateCategory = DB::table("finish_products_mst as a")
            ->select("a.f_sub_category_id as id", "b.name")
            ->join("f_product_sub_category as b", "a.f_sub_category_id", "b.id")
            ->distinct("a.f_sub_category_id")
            ->where("a.department_id", $id)->get();

        return view("department-product", compact("department", "category", "department_product", "allocateCategory"));
    }

    public function AllocateDepartmentProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
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


            DB::table('finish_products_mst')->where("f_sub_category_id", $value)->update(array(
                "department_id" => $request->department_id,
            ));
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function UnAllocateDepartmentProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [

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
            DB::table('finish_products_mst')->where("f_sub_category_id", $value)->update(array(
                "department_id" => 0,
            ));
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function ModeOfTransport(Request $request)
    {
        $data =  DB::table("mode_of_transport")->get();
        return view("mode-of-transport", compact("data"));
    }

    public function  SaveModeOfTransport(Request $request)
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
                DB::table('mode_of_transport')->insertGetId(array(
                    "name" => $request->name,
                    "contact_person" => $request->contact_person,
                    "number" => $request->number,
                    "vehicle_no" => $request->vehicle_no,
                ));
            } else {
                DB::table('mode_of_transport')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "contact_person" => $request->contact_person,
                    "vehicle_no" => $request->vehicle_no,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function OrderType(Request $request)
    {

        $data = DB::table("order_type")->get();
        foreach ($data as $key => $value) {
            $subCategoryIds = explode(",", $value->f_sub_category_id);
            $subCategories = DB::table("f_product_sub_category")
                ->whereIn("id", $subCategoryIds)
                ->pluck("name") // Get only the 'name' column
                ->toArray(); // Convert collection to an array

            // Replace sub_category ID string with actual names
            $value->sub_category_name = implode(", ", $subCategories);
        }


        $sub_category = DB::table("f_product_sub_category")->orderBy("name", "asc")->get();

        return view("order-type", compact('data', "sub_category"));
    }

    public function SaveOrderType(Request $request)
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
                DB::table('order_type')->insertGetId(array(
                    "name" => $request->name,
                    "days" => $request->days,
                    "type" => $request->type,
                    "week_days" => implode(", ", $request->week_days),
                    "f_sub_category_id" => implode(", ", $request->f_sub_category_id),

                ));
            } else {
                DB::table('order_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,
                    "days" => $request->days,
                    "type" => $request->type,
                    "week_days" => implode(", ", $request->week_days),
                    "f_sub_category_id" => implode(", ", $request->f_sub_category_id),

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }



    public function GetCustomerOutlet(Request $request)
    {
        if ($request->type == "customer") {
            return  DB::table("customers")
                ->orderBy("name", "asc")
                ->get();
        } else {
            return DB::table("outlet")
                ->select("id", "outlet_name as name")
                ->orderBy("outlet_name", "asc")
                ->get();
        }
    }

    public function GetCustomerOutletList(Request $request)
    {
        if ($request->order_type == "customer") {
            return DB::table("customers")->orderBy("name", "asc")->get();
        } else {
            return DB::table("outlet")->select("*", "outlet_name as name")->orderBy("outlet_name", "asc")->get();
        }
    }

    public function updateVendorProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'active' => 'required',


        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'msg' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }
        try {
            DB::table("vendor_product")->where("id", $request->id)->update(array(
                "active" => $request->active
            ));
        } catch (\Throwable $th) {

            return response()->json([
                'error' => true,
                'msg' => $th->getMessage(),
                'data' => null,
            ], 422);
        }

        return response()->json([
            'error' => false,
            'msg' => "Save Successfully",
            'data' => null,
        ], 200);
    }

    public function Outlet_customer(Request $request)
    {
        $outlet =  DB::table("outlet_customers")->get();
        $customer_type =  DB::table("customer_type")->get();
        return view("outlet_customer", compact("outlet", "customer_type"));
    }

    public function SaveOutletCustomer(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'number' => 'required',
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
        $customer_id = 0;
        try {
            if (!empty($request->id)) {
                DB::table('outlet_customers')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                ));
                $customer_id = $request->id;
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function kot(Request $request)
    {
        $fromDt = $request->input("fromDt") ?: Carbon::now()->startOfMonth()->toDateString();
        $toDt = $request->input("toDt") ?: Carbon::now()->toDateString();

        $status = request("status", "dispatch");

        $outlet = DB::table("outlet_customer_order_mst as a")
            ->select("a.*", "b.name as outlet_name")
            ->join("outlet_users as b", "a.outlet_id", "=", "b.id")
            ->where("invoice_type", "draft")
            ->orderBy("id", "desc");

        if ($fromDt) {
            $outlet->whereDate("a.order_date", ">=", $fromDt);
        }
        if ($toDt) {
            $outlet->whereDate("a.order_date", "<=", $toDt);
        }
        $data = $outlet->get();

        $customers = DB::table("customers")->get();
        return view("kot", compact("data"));
    }

    public function deletekot(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required',
                'order_pwd' => 'required|string',
            ]);

            $company_setting = DB::table("company_settings")->where("id", $request->user->id)->select('order_pwd')->first();
            if ($request->order_pwd !== $company_setting->order_pwd) {
                return  redirect()->back()->with("error", 'Incorrect password.');
            }


            DB::table('outlet_customer_order_mst')->where("id", $validated['id'])->delete();
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }

        return  redirect()->back()->with("success", "Deleted Successfully");
    }
}
