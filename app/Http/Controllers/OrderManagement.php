<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class OrderManagement extends Controller
{

    // public function NewOrder(Request $request)
    // {
    //     $customers = DB::table("customers")->where("active", 1)->get();
    //     $brand = DB::table("brand")->get();
    //     $store = DB::table("store")->get();

    //     return view("new-order", compact("customers", "brand", "store"));
    // }


    // public function UploadRequirementList(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|mimes:csv,txt',
    //     ]);


    //     if ($validator->fails()) {
    //         $messages = $validator->errors();
    //         $count = 0;
    //         foreach ($messages->all() as $error) {
    //             if ($count == 0)
    //                 return json_encode(['error' => $error]);

    //             $count++;
    //         }
    //     }

    //     $count_d = 0;
    //     if ($request->hasFile('file')) {
    //         $file = $request->file('file');
    //         $filePath = $file->store('csv', 'public');

    //         $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
    //         // $csv->setHeaderOffset(0); // Assuming the first row contains headers
    //         $data = [];
    //         foreach ($csv as $record) {
    //             $products = DB::table("products as a")->select("a.*", "b.name as brand_name")
    //                 ->join("brand as b", "a.brand_id", "b.id")
    //                 ->where('article_no', $record[0])->first();
    //             if ($products) {
    //                 $products->qty = $record[1];
    //             }
    //             $productIds = array_column($data, 'id');
    //             if (!in_array($products->id, $productIds)) {
    //                 $data[] = $products;
    //             }
    //         }
    //         return json_encode(['data' => $data]);
    //     }

    //     return json_encode(['error' => "No csv file selected for upload"]);
    // }


    // public function SaveNewOrder(Request $request)
    // {

    //     $checkUser = CheckUserPassword($request->password);
    //     if (!$checkUser) {
    //         return redirect()->back()->with("error", "Incorrect Password");
    //     }


    //     $validator = Validator::make($request->all(), [
    //         'customer_id' => 'required',
    //         'location_id' => 'required',

    //     ]);

    //     if ($validator->fails()) {
    //         $messages = $validator->errors();
    //         $count = 0;
    //         foreach ($messages->all() as $error) {
    //             if ($count == 0)
    //                 return redirect()->back()->with('error', $error);
    //             $count++;
    //         }
    //     }

    //     $prod_list = json_decode($request->prod_list);
    //     if (!$prod_list) {
    //         return redirect()->back()->with('error', "Select at least one product");
    //     }
    //     $order_id = 'ORD_' . date('dmyhis');

    //     try {

    //         $mst_id =  DB::table('order_mst')->insertGetId(array(
    //             "customer_id" => $request->customer_id,
    //             "packing_date" => $request->packing_date,
    //             "delivery_date" => $request->delivery_date,
    //             "description" => $request->description,
    //             "user_id" => $checkUser->id,
    //             "location_id" => $request->location_id,
    //             "order_id" => $order_id,


    //         ));
    //         foreach ($prod_list as $key => $value) {
    //             DB::table('order_det')->insertGetId(array(
    //                 "mst_id" => $mst_id,
    //                 "product_id" => $value->id,
    //                 "qty" => $value->qty,
    //                 "price" => $value->price,
    //             ));
    //         }
    //     } catch (\Throwable $th) {
    //         return redirect()->back()->with('error', $th->getMessage());
    //     }
    //     return  redirect()->back()->with("success", "Save Successfully");
    // }

    // public function Orders(Request $request, $status)
    // {
    //     if (empty($status)) {
    //         return  redirect()->back()->with("error", "Status not found");
    //     }

    //     $orders = DB::table("order_mst as a")
    //         ->select("a.*", "b.name as customer", "c.name as user")
    //         ->join("customers as b", "a.customer_id", "b.id")
    //         ->join("users as c", "a.user_id", "c.id")
    //         ->where("a.status", $status)
    //         ->get();
    //     return view("orders", compact("orders"));
    // }
    // public function InitiateOrder(Request $request)
    // {

    //     $booked_qty = 0;
    //     $pending_qty = 0;
    //     $validator = Validator::make($request->all(), [
    //         'order_id' => 'required',

    //     ]);

    //     if ($validator->fails()) {
    //         $messages = $validator->errors();
    //         $count = 0;
    //         foreach ($messages->all() as $error) {
    //             if ($count == 0)
    //                 return redirect()->back()->with('error', $error);
    //             $count++;
    //         }
    //     }

    //     $order_mst = DB::table("order_mst")->where("id", $request->order_id)->first();
    //     if (!$order_mst) {
    //         return  redirect()->back()->with("error", "Order not found");
    //     }
    //     if ($order_mst->initiate == 0) {
    //         $order_det = DB::table("order_det")->where("mst_id", $order_mst->id)->get();
    //         if (!$order_det) {
    //             return  redirect()->back()->with("error", "Order product not found");
    //         }
    //         DB::table("order_mst")->where("id", $request->order_id)->update(array(
    //             "initiate" => 1,
    //             "status" => 'processing'
    //         ));


    //         $check_require = [];
    //         foreach ($order_det as $key => $value) {
    //             $po_id = 'PO_' . date('dmyhis');

    //             $product = DB::table("products")->where("id", $value->product_id)->first();
    //             if ($product->vendor_id > 0) {
    //                 $current_stock = DB::table("current_stock")->where("id", $value->product_id)->first();
    //                 if (!$current_stock) {

    //                     if (empty($check_require)) {
    //                         $mst_id = DB::table('po_mst')->insertGetId(array(
    //                             "vendor_id" => $product->vendor_id,
    //                             "user_id" => $request->user->id,
    //                             "order_id" => $request->order_id,
    //                             "po_id" => $po_id,
    //                         ));
    //                         DB::table('po_det')->insertGetId(array(
    //                             "mst_id" => $mst_id,
    //                             "product_id" => $product->id,
    //                             "qty" => $value->qty,
    //                             "price" => $product->price,
    //                         ));
    //                         $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
    //                     } else {
    //                         if (!empty($check_require)) {
    //                             $find_value = 0;
    //                             $mst_id = 0;
    //                             foreach ($check_require as $key) {
    //                                 if ($key['vendor_id'] == $product->vendor_id) {
    //                                     $find_value = 1;
    //                                     $mst_id = $key['mst_id'];
    //                                     break;
    //                                 }
    //                             }
    //                             if ($find_value == 0) {
    //                                 $mst_id = DB::table('po_mst')->insertGetId(array(
    //                                     "vendor_id" => $product->vendor_id,
    //                                     "user_id" => $request->user->id,
    //                                     "order_id" => $request->order_id,
    //                                     "po_id" => $po_id,
    //                                 ));


    //                                 DB::table('po_det')->insertGetId(array(
    //                                     "mst_id" => $mst_id,
    //                                     "product_id" => $product->id,

    //                                     "qty" => $value->qty,
    //                                     "price" => $product->price,
    //                                 ));

    //                                 $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
    //                             } else {
    //                                 DB::table('po_det')->insertGetId(array(
    //                                     "mst_id" => $mst_id,
    //                                     "product_id" => $product->id,

    //                                     "qty" => $value->qty,
    //                                     "price" => $product->price,
    //                                 ));
    //                             }
    //                         }
    //                     }


    //                     DB::table('order_det')->where("id", $value->id)->update(array(
    //                         "pending_qty" => $value->qty,
    //                     ));
    //                 } else {


    //                     $booked_qty = $current_stock->stock - $value->qty;

    //                     if ($booked_qty < 0) {
    //                         $pending_qty =   $value->qty - $current_stock->stock;

    //                         if (empty($check_require)) {
    //                             $mst_id = DB::table('po_mst')->insertGetId(array(
    //                                 "vendor_id" => $product->vendor_id,
    //                                 "user_id" => $request->user->id,
    //                                 "order_id" => $request->order_id,
    //                                 "po_id" => $po_id,
    //                             ));
    //                             DB::table('po_det')->insertGetId(array(
    //                                 "mst_id" => $mst_id,
    //                                 "product_id" => $product->id,
    //                                 "qty" => $value->qty,
    //                                 "price" => $product->price,
    //                             ));
    //                             $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
    //                         } else {
    //                             if (!empty($check_require)) {
    //                                 $find_value = 0;
    //                                 $mst_id = 0;
    //                                 foreach ($check_require as $key) {
    //                                     if ($key['vendor_id'] == $product->vendor_id) {
    //                                         $find_value = 1;
    //                                         $mst_id = $key['mst_id'];
    //                                         break;
    //                                     }
    //                                 }
    //                                 if ($find_value == 0) {
    //                                     $mst_id = DB::table('po_mst')->insertGetId(array(
    //                                         "vendor_id" => $product->vendor_id,
    //                                         "user_id" => $request->user->id,
    //                                         "order_id" => $request->order_id,
    //                                         "po_id" => $po_id,
    //                                     ));


    //                                     DB::table('po_det')->insertGetId(array(
    //                                         "mst_id" => $mst_id,
    //                                         "product_id" => $product->id,

    //                                         "qty" => $value->qty,
    //                                         "price" => $product->price,
    //                                     ));

    //                                     $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
    //                                 } else {
    //                                     DB::table('po_det')->insertGetId(array(
    //                                         "mst_id" => $mst_id,
    //                                         "product_id" => $product->id,

    //                                         "qty" => $value->qty,
    //                                         "price" => $product->price,
    //                                     ));
    //                                 }
    //                             }
    //                         }

    //                         DB::table('current_stock')->where("product_id", $value->product_id)->where("location_id", $order_mst->location_id)->update([
    //                             'stock' => DB::raw('stock - ' . $current_stock->stock),
    //                             // other fields to update
    //                         ]);;

    //                         DB::table('order_det')->where("id", $value->id)->update(array(
    //                             "pending_qty" => $pending_qty,
    //                             "booked_qty" => $current_stock->stock,
    //                         ));
    //                     } else {

    //                         DB::table('current_stock')->where("product_id", $value->product_id)->where("location_id", $order_mst->location_id)->update([
    //                             'stock' => DB::raw('stock - ' . $value->qty),
    //                             // other fields to update
    //                         ]);;

    //                         DB::table('order_det')->where("id", $value->id)->update(array(

    //                             "booked_qty" => $value->qty,
    //                         ));
    //                     }
    //                 }
    //             }
    //         }
    //     } else {
    //         return  redirect()->back()->with("error", "Already Initiated");
    //     }

    //     die;
    // }


    public function CreateOrder(Request $request)
    {
        $customers = DB::table("customers")->get();
        $store = DB::table("store")->get();
        $finish_products_mst = DB::table("finish_products_mst")->get();
        $gst = DB::table("gst")->get();
        $order_type = DB::table("order_type")->orderBy("name", "asc")->get();

        return view("create-order", compact("customers", "store", "finish_products_mst", "gst", "order_type"));
    }

    public function GetPendingTaskList(Request $request)
    {
        $gen_set_mst = DB::table("gen_set_mst")
            ->where("team_id", $request->id)
            ->where("location_id", $request->location_id)
            ->where("f_product_id", $request->product_id)
            ->where("is_order", 0)
            ->where("order_id", 0)
            ->orderBy("delivery_date", "asc")
            ->get();
        return json_encode(["gen_set_mst" => $gen_set_mst]);
    }

    public function SaveOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'customer_id' => 'required',




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
        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }
        $order_id = 'ORD_' . date('dmyhis');


        try {
            $mst_id =  DB::table('order_mst')->insertGetId(array(
                "customer_id" => $request->customer_id,
                "user_id" => $request->user->id,

                "order_id" => $order_id,
                "status" => "pending",
                "order_date" => $request->order_date,
                "delivery_date" => $request->delivery_date,
                "description" => $request->description,
                "order_type_id" => $request->order_type_id,
                "order_type" => $request->order_type,

            ));

            foreach ($prod_list as $key => $value) {
                $finish_products_mst =   DB::table("finish_products_mst")->where("id", $value->product_id)->first();
                DB::table('order_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "gst" => $value->gst,
                    "gst_type" => $value->gst_type,
                    "cess_amt" => $finish_products_mst->cess_tax,
                    "mrp" => $finish_products_mst->price,
                ));
            }
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }





        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function GeneratePOProduct(Request $request, $id = 0)
    {
        $gen_set_mst = DB::table("gen_set_mst as a")->select("a.*", "b.name as product")->join("finish_products_mst as b", "a.f_product_id", "b.id")->where('status', "pending")->get();

        $products = DB::table("products")->get();

        return view("generate-po-product", compact("gen_set_mst", "products", "id"));
    }



    public function GetGenSetProduct(Request $request)
    {
        $gen_set_det = DB::table("gen_set_det as a")
            ->select(
                "a.*",
                "p.name as product_name",
                "p.article_no as article_no",
                "p.gst as gst",
                DB::raw("COALESCE(b.stock, 0) as stock")
            )
            ->leftJoin("current_stock as b", function ($join) {
                $join->on("a.product_id", "=", "b.product_id")
                    ->on("a.location_id", "=", "b.location_id");
            })
            ->join("products as p", "a.product_id", "=", "p.id")
            ->where("a.mst_id", $request->id)
            ->get();
        return $gen_set_det;
    }

    public function SavePoProducts(Request $request)
    {




        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }

        $error = "";
        $row = 0;
        $check_require = [];
        foreach ($prod_list as $key => $value) {
            $po_id = 'PO_' . date('dmyhis');

            $product = DB::table("products")->where("id", $value->product_id)->first();
            if ($product->vendor_id > 0) {

                if (empty($check_require)) {
                    $mst_id = DB::table('po_mst')->insertGetId(array(
                        "vendor_id" => $product->vendor_id,
                        "user_id" => $request->user->id,
                        "po_id" => $po_id,
                    ));
                    DB::table('po_det')->insertGetId(array(
                        "mst_id" => $mst_id,
                        "product_id" => $product->id,
                        "qty" => $value->qty,
                        "price" => $value->price,
                        "gst" => $product->gst,
                        "gst_type" => $value->gst_type,
                    ));
                    $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                } else {
                    if (!empty($check_require)) {
                        $find_value = 0;
                        $mst_id = 0;
                        foreach ($check_require as $key) {
                            if ($key['vendor_id'] == $product->vendor_id) {
                                $find_value = 1;
                                $mst_id = $key['mst_id'];
                                break;
                            }
                        }
                        if ($find_value == 0) {
                            $mst_id = DB::table('po_mst')->insertGetId(array(
                                "vendor_id" => $product->vendor_id,
                                "user_id" => $request->user->id,
                                "po_id" => $po_id,
                            ));


                            DB::table('po_det')->insertGetId(array(
                                "mst_id" => $mst_id,
                                "product_id" => $product->id,
                                "qty" => $value->qty,
                                "price" => $value->price,
                                "gst" => $product->gst,
                                "gst_type" => $value->gst_type,
                            ));

                            $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                        } else {
                            DB::table('po_det')->insertGetId(array(
                                "mst_id" => $mst_id,
                                "product_id" => $product->id,
                                "qty" => $value->qty,
                                "price" => $value->price,
                                "gst" => $product->gst,
                                "gst_type" => $value->gst_type,
                            ));
                        }
                    }
                }
            } else {
                $error .= "Vendor Not Found";
                $row++;
            }
        }
        return  redirect("purchase-order/pending")->with("success", "Save Successfully : Vendor not found : " . $row);
    }

    public function Orders(Request $request, $status)
    {
        if (empty($status)) {
            return  redirect()->back()->with("error", "Status not found");
        }

        $date = request("date", date("Y-m-d", strtotime("+1 day")));

        $type = request("type", "daily");
        $order = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer", "c.name as user", "d.name as category")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")
            ->join("order_type as d", "a.order_type_id", "d.id")
            ->where("a.status", $status)
            ->where("order_type", "customer")
            ->whereIn("a.user_id", $request->userIds);

        $order->whereDate("a.delivery_date", $date);
        $order->where("d.type", $type);




        $outlet = DB::table("order_mst as a")
            ->select("a.*", "b.outlet_name as customer", "c.name as user", "d.name as category")
            ->join("outlet as b", "a.customer_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")
            ->join("order_type as d", "a.order_type_id", "d.id")
            ->where("a.status", $status)
            ->where("order_type", "outlet")
            ->whereIn("a.user_id", $request->userIds);

        $outlet->whereDate("a.delivery_date", $date);
        $outlet->where("d.type", $type);



        $orders = $order->union($outlet)->orderBy("id", "desc")->get();






        $department = DB::table("department")->first();

        $customers = DB::table("customers")->get();
        return view("orders", compact("orders", "customers", "status", "department"));
    }
    public function SaveOrderStatus(Request $request)
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
        DB::table('order_mst')->where("id", $request->id)->update(array(
            "status" => $request->status,
            "dispatch_date" => $request->dispatch_date,
            "delivery_date" => $request->delivered_date,
        ));
        return  redirect()->back()->with("success", "Save successfully");
    }

    public function OrderView(Request $request, $id)
    {

        if (empty($id)) {
            return  redirect()->back()->with("error", "ID not found");
        }
        $customer_order = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer_name", "b.number", "b.email", "b.gst", "b.address", "b.state", "b.city", "b.pincode")
            ->join("customers as b", "a.customer_id", "b.id")
            ->where("a.id", $id)
            ->where("a.order_type", "customer")
            ->first();
        $outlet_order = DB::table("order_mst as a")
            ->select("a.*", "b.outlet_name as customer_name", "b.number", "c.email", "c.gst_no as gst", "c.address")
            ->join("outlet as b", "a.customer_id", "b.id")
            ->join("company_settings as c", "c.outlet_id", "b.id")
            ->where("a.id", $id)
            ->where("a.order_type", "outlet")
            ->first();


        $order_mst = $customer_order ?? $outlet_order;

        $order_det = DB::table("order_det as a")
            ->select("a.*", "b.name as product", "c.name as sub_category")
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->join("f_product_sub_category as c", "b.f_sub_category_id", "c.id")
            ->where("a.mst_id", $id)
            ->get();



        return view("order-view", compact("order_mst", "order_det"));
    }


    public function ShiftOrder(Request $request)
    {




        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'customer_id' => 'required',


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

        DB::table('order_mst')->where("id", $request->id)->update(array(
            "customer_id" => $request->customer_id,

        ));
        return  redirect()->back()->with("success", "Save successfully");
    }

    public function DeleteGenSetDet(Request $request)
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

        $gen_set_det =  DB::table('gen_set_det')->where('id', $request->id)->first();

        DB::table('current_stock')->where("location_id", $gen_set_det->location_id)->where("product_id", $gen_set_det->product_id)->update([
            'stock' => DB::raw('stock + ' . $gen_set_det->booked_qty)
        ]);

        DB::table('gen_set_det')->where('id', $gen_set_det->id)->delete();
        return  redirect()->back()->with("success", "Save successfully");
    }

    public function AddGenSetDet(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'product_id' => 'required',
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

        $gen_set_det =  DB::table('gen_set_det')->where('product_id', $request->product_id)->where("mst_id", $request->id)->first();
        $products =  DB::table('products')->where('id', $request->product_id)->first();
        if ($gen_set_det) {
            return  redirect()->back()->with("error", "Product already added");
        }
        DB::table('gen_set_det')->insertGetId(array(
            "mst_id" => $request->id,

            "product_id" => $request->product_id,
            "location_id" => $request->location_id,
            "qty" => $request->qty,
            "price" => $products->price

        ));

        return  redirect()->back()->with("success", "Save successfully");
    }

    public function OrderSummary(Request $request)
    {
        $date = request("date", date("Y-m-d"));

        $orders = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer", "c.name as user")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")

            ->where("a.status", "pending")
            ->whereDate("a.delivery_date", $date)
            ->whereIn("a.user_id", $request->userIds)
            ->orderBy("id", "desc")
            ->get();


        $customers = DB::table("customers")->get();
        return view("order-summary", compact("orders", "customers"));
    }

    public function GenerateWorkOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'order_ids' => 'required',
            'date' => 'required',

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

        $department_id = [];

        foreach ($request->order_ids as $key => $value) {
            $order_det =  DB::table("order_det as a")
                ->select("a.*", "b.department_id")
                ->join("finish_products_mst as b", "a.product_id", "b.id")
                ->where("a.mst_id", $value)->get();
            foreach ($order_det as $k => $v) {
                if ($v->qty - $v->booked_qty > 0) {


                    $mst_id =  DB::table("work_order_mst")->where("department_id", $v->department_id)->whereDate("delivery_date", $request->date)->first();
                    if (!$mst_id) {
                        $mst_id =   DB::table("work_order_mst")->insertGetId(array(
                            "department_id" => $v->department_id,
                            "delivery_date" => $request->date,
                        ));
                        DB::table("work_order_det")->insertGetId(array(
                            "mst_id" => $mst_id,
                            "product_id" => $v->product_id,
                            "order_id" => $v->mst_id,
                            "qty" => $v->qty - $v->booked_qty,
                        ));
                        $department_id[] = array("department_id" => $v->department_id, "mst_id" => $mst_id, "delivery_date" => $request->date);
                    } else {
                        // $v->department_id=1;
                        // $department_id[] = array("department_id" => $v->department_id, "mst_id" => $mst_id->id, "delivery_date" => "2025-01-07");

                        if (empty($department_id)) {
                            $department_id[] = array("department_id" => $v->department_id, "mst_id" => $mst_id->id, "delivery_date" => $request->date);
                        }

                        if (!empty($department_id)) {
                            $find_value = 0;
                            $mst_id = 0;

                            foreach ($department_id as $i) {

                                if ($i['department_id'] == $v->department_id && $i['delivery_date'] == $request->date) {
                                    $find_value = 1;
                                    $mst_id = $i['mst_id'];
                                    break;
                                }
                            }
                            if ($find_value == 1) {
                                DB::table("work_order_det")->insertGetId(array(
                                    "mst_id" => $mst_id,
                                    "product_id" => $v->product_id,
                                    "order_id" => $v->mst_id,
                                    "qty" => $v->qty - $v->booked_qty,
                                ));
                            }
                        }
                    }
                }
            }
            DB::table("order_mst")->where("id", $value)->update(array(
                "status" => "processing",
            ));
        }
        return  redirect()->back()->with("success", "Save successfully");
    }


    public function OrderSummaryDepartmentWise(Request $request)
    {

        $id = request("id", 0);
        $date = request("date", date("Y-m-d"));
        $department =  DB::table("department")->get();
        $department_details = collect();

        $department_details =  DB::table("work_order_mst as a")
            ->select("a.*", "b.name", "b.contact_person", "b.number")
            ->join("department as b", "a.department_id", "b.id")
            ->where("a.department_id", $id)
            ->whereDate("a.delivery_date", $date)
            ->first();
        $work_order_det = collect();
        if ($department_details) {


            $work_order_det = DB::table("work_order_det as a")
                ->select("b.name", DB::raw("sum(a.qty) as qty"))
                ->join("finish_products_mst as b", "a.product_id", "b.id")
                ->where("a.mst_id", $department_details->id)
                ->groupBy("a.product_id", "b.name")
                ->get();
        }
        return view("order-summary-department-wise", compact("department", "department_details", "work_order_det"));
    }

    public function OrderSummaryCustomerWise(Request $request)
    {

        $id = request("id", 0);
        $date = request("date", date("Y-m-d"));

        // $work_order_mst = DB::table("work_order_mst")->where("department_id", $id)->whereDate("delivery_date", $date)->first();


        // $work_order_det = DB::table("work_order_det as a")
        // ->select("c.name as customer","c.id")
        // ->join("order_mst as b","a.order_id","b.id")
        // ->join("customers as c","b.customer_id","c.id")
        // ->where("a.mst_id", $work_order_mst->id)
        // ->get();
        // echo "<pre>";
        // print_r($work_order_det);
        // die;




        // $order_mst =  DB::table("order_mst as a")
        //     ->select("a.*", "b.name as customer")
        //     ->join("customers as b", "a.customer_id", "b.id")
        //     ->whereDate("delivery_date", $date)
        //     ->where("status", "processing")
        //     ->get();

        // foreach ($order_mst as $key) {
        //     $det = $attendance_report = DB::table("work_order_det")
        //         ->select("product_id")
        //         ->where("order_id", $key->id)->get();
        //     foreach ($det as $k => $v) {
        //         $product_id[] = $v->product_id;
        //     }
        // }

        // $product_id = array_unique($product_id);
        // sort($product_id);
        // $report = [];
        // foreach ($order_mst as $key) {

        //     $emp_data = [];
        //     $emp_data[] =  array('name' => $key->customer);
        //     foreach ($product_id as $j => $i) {

        //         $details = DB::table("work_order_det as a")
        //             ->select("b.name as product", "a.product_id", "a.qty")
        //             ->join("finish_products_mst as b", "a.product_id", "b.id")
        //             ->where("order_id", $key->id)->where("product_id", $i)->first();
        //         if ($details) {
        //             $emp_data[] =  array('product_id' => $i, "product" => $details->product, "qty" => $details->qty);
        //         } else {
        //             $emp_data[] =  array('product_id' => $i, "product" => "", "qty" => 0);
        //         }
        //     }


        //     $report[] = $emp_data;
        // }

        $product_id = [];
        $order_id = [];

        $department_details = DB::table('department')->where("id", $id)->first();
        $work_order_mst = DB::table("work_order_mst as a")
            ->select("b.product_id", "b.order_id")
            ->join("work_order_det as b", "a.id", "b.mst_id")
            ->where("a.delivery_date", $date)
            ->where("a.department_id", $id)
            ->get();

        foreach ($work_order_mst as $value) {
            $product_id[] = $value->product_id;
            $order_id[] = $value->order_id;
        }

        $product_id = array_unique($product_id);
        sort($product_id);

        $order_id = array_unique($order_id);
        sort($order_id);

        $report = [];

        foreach ($product_id as $product) {
            $f_product = DB::table("finish_products_mst as a")
                ->select("a.*", "b.name as sub_category")
                ->join("f_product_sub_category as b", "a.f_sub_category_id", "b.id")
                ->where("a.id", $product)
                ->first();

            $emp_data = [['name' => $f_product->name, "sub_category" => $f_product->sub_category]];

            foreach ($order_id as $order) {
                $customer = DB::table("order_mst as a")
                    ->select("b.nickname as customer", "b.id as customer_id", "c.qty")
                    ->join("customers as b", "a.customer_id", "b.id")
                    ->join("work_order_det as c", "a.id", "c.order_id")
                    ->where("a.id", $order)
                    ->where("c.product_id", $product)
                    ->where("a.order_type", "customer")
                    ->first();

                $outlet = DB::table("order_mst as a")
                    ->select("b.nickname as customer", "b.id as customer_id", "c.qty")
                    ->join("outlet as b", "a.customer_id", "b.id")
                    ->join("work_order_det as c", "a.id", "c.order_id")
                    ->where("a.id", $order)
                    ->where("c.product_id", $product)
                    ->where("a.order_type", "outlet")
                    ->first();

                $details = $customer ?? $outlet;

                // Ensure Lucky Outlet shows 0 if no quantity is found
                $emp_data[] = [
                    'order_id' => $order,
                    'customer' => $details->customer ?? '',
                    'qty' => $details->qty ?? 0,  // Default to 0 if no quantity
                    'product_id' => $product,
                    'customer_id' => $details->customer_id ?? 0,
                ];
            }

            $report[] = $emp_data;
        }

        // Ensure unique customer listing and handling missing quantities
        $customers = collect();
        foreach ($order_id as $order) {
            $customer = DB::table("order_mst as a")
                ->select("b.nickname as customer")
                ->join("customers as b", "a.customer_id", "b.id")
                ->where("a.id", $order)
                ->where("a.order_type", "customer")
                ->first();

            $outlet = DB::table("order_mst as a")
                ->select("b.nickname as customer")
                ->join("outlet as b", "a.customer_id", "b.id")
                ->where("a.id", $order)
                ->where("a.order_type", "outlet")
                ->first();

            $customers[] = $customer ?? $outlet;
        }

        $department = DB::table("department")->get();
        return view("order-summary-customer-wise", compact("report", "customers", "department", "department_details"));
    }

    public function GetCustomerTypeProducts(Request $request)
    {
        if ($request->order_type == "customer") {
            $customers = DB::table("customers")->where("id", $request->customer_id)->first();
        } else {
            $customers = DB::table("outlet")->where("id", $request->customer_id)->first();
        }

        $order_type = DB::table("order_type")->where("id", $request->order_type_id)->first();

        $customer_type_product =  DB::table("customer_type_product as a")
            ->select("a.*", "b.name as name", "b.id as id", "b.gst",   DB::raw("CASE WHEN c.stock IS NOT NULL THEN c.stock ELSE 0 END as stock"))
            ->join("finish_products_mst as b", "a.finish_product_id", "b.id")
            ->leftJoin("finish_product_stock as c", "b.id", "c.product_id")
            ->join("f_product_sub_category as e", "b.f_sub_category_id", "e.id")
            ->where("a.customer_type_id", $customers->customer_type_id)
            ->whereIn("e.id", explode(', ', $order_type->f_sub_category_id))
            ->orderBy("b.name", "asc")
            ->get();
        return $customer_type_product;
    }


    public function CompleteProduction(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'order_ids' => 'required',


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

        $order_mst_ids = DB::table("order_mst")
            ->whereIn("id", $request->order_ids)
            ->where("status", "processing")

            ->pluck('id');


        $order_det = DB::table("order_det as a")
            ->select("a.product_id", DB::raw("SUM(a.qty) as qty"))
            ->join("finish_products_mst as b", "a.product_id", "b.id")
            ->whereIn("a.mst_id", $order_mst_ids)
            ->groupBy("a.product_id")
            ->get();

        if (!empty($order_mst_ids)) {

            $finish_inward_mst =  DB::table("finish_inward_mst")->whereDate("date", now())->first();
            if (empty($finish_inward_mst)) {


                $mst_id = DB::table("finish_inward_mst")->insertGetId(array(
                    "date" => now()
                ));
                foreach ($order_det as $key => $value) {
                    DB::table("finish_inward_det")->insertGetId(array(
                        "mst_id" => $mst_id,
                        "product_id" => $value->product_id,
                        "qty" => $value->qty
                    ));
                }
            } else {
                DB::table("finish_inward_mst")->whereDate("date", now())->update(array("status" => 0));
                foreach ($order_det as $key => $value) {

                    $finish_inward_det =  DB::table("finish_inward_det")->where("product_id", $value->product_id)->where("mst_id", $finish_inward_mst->id)->first();

                    if ($finish_inward_det) {
                        DB::table("finish_inward_det")->where("product_id", $value->product_id)->where("mst_id", $finish_inward_mst->id)->increment("qty", $value->qty);
                    } else {
                        DB::table("finish_inward_det")->insertGetId(array(
                            "mst_id" => $finish_inward_mst->id,
                            "product_id" => $value->product_id,
                            "qty" => $value->qty
                        ));
                    }
                }
            }
        }
        $order_mst_ids = DB::table("order_mst")

            ->whereIn("id", $order_mst_ids)->update(array(
                "status" => "dispatch"
            ));

        return  redirect()->back()->with("success", "Save successfully");
    }

    public function OrderSummaryShopWise(Request $request)
    {
        $date = request("date", date("Y-m-d"));
        $customer_id = request("customer_id");
        $order_id = request("order_id");
        $sub_category_id = request("sub_category_id");
        $outlet =  DB::table("outlet")->get();

        $selected_sub_category = collect();
        $work_order_det = collect();
        $selected_order = collect();






        if ($order_id) {



            $work_order =    DB::table("work_order_det as a")
                ->select("a.*", "d.name as product", "e.name as sub_category", "f.name as category")
                ->join("order_mst as b", "a.order_id", "b.id")

                ->join("finish_products_mst as d", "a.product_id", "d.id")
                ->join("f_product_sub_category as e", "d.f_sub_category_id", "e.id")
                ->join("f_product_category as f", "d.f_category_id", "f.id");
            if ($request->order_id) {
                $work_order->whereIn("b.id", $request->order_id);
                $selected_order = DB::table("work_order_det as a")
                    ->select("b.id", "b.order_id")
                    ->join("order_mst as b", "a.order_id", "b.id")



                    ->whereIn("b.id", $request->order_id)
                    ->groupBy("b.id", "b.order_id")
                    ->get();
            }


            // $work_order->where("a.mst_id", $work_order_mst->id);

            $work_order_det = $work_order->get();
        }






        if (request("type") == "customer") {
            $customer_details = DB::table("customers")->where("id", $customer_id)->first();
        } else {
            $customer_details = DB::table("outlet")->select("outlet_name as name", "number as email")->where("id", $customer_id)->first();
        }

        $f_product_sub_category =  DB::table("f_product_sub_category")->get();



        return view("order-summary-shop-wise", compact("outlet", "work_order_det", "customer_details", "f_product_sub_category", "selected_order"));
    }

    public function GetWordOrder(Request $request)
    {

        return     DB::table("order_mst as a")
            ->select("a.*", "b.name as order_type")
            ->join("order_type as b", "a.order_type_id", "b.id")
            ->where("a.order_type", $request->type)->where("a.customer_id", $request->customer_id)->whereDate("a.delivery_date", $request->date)->where("a.status", "!=", "pending")->get();
    }

    public function CancelOrder(Request $request)
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
            DB::table('order_mst')->where("id", $request->id)->update(array(
                "status" => "cancel",

            ));
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function ConvertToInvoice(Request $request)
    {
        $company_settings =   DB::table("company_settings")->where("id", 1)->first();
        $invoice = $company_settings->invoice_prefix . $company_settings->invoice_no;

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
            DB::table('outward_customer_order_mst')->where("id", $request->id)->update(array(
                "invoice_no" => $invoice,
                "is_invoice" => 1,
            ));
            DB::table("company_settings")->where("id", 1)->increment("invoice_no", 1);
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function convertInvoiceDelivered(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'updateType' => 'required',
            "outward_ids" => "required"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

      
        $company_settings =   DB::table("company_settings")->where("id", 1)->first();
        $invoice = $company_settings->invoice_prefix . $company_settings->invoice_no;
        try {

            foreach ($request->outward_ids as $key => $value) {

 
                if ($request->updateType == "delivered") {

                    DB::table("outward_customer_order_mst")->where("id", $value)->update(array(
                        "status" => "delivered"
                    ));
                } else if ($request->updateType == "invoice") {
                    DB::table('outward_customer_order_mst')->where("id", $value)->update(array(
                        "invoice_no" => $invoice,
                        "is_invoice" => 1,
                    ));
                    DB::table("company_settings")->where("id", 1)->increment("invoice_no", 1);
                } else {
                    DB::table('outward_customer_order_mst')->where("id", $value)->update(array(
                        "invoice_no" => $invoice,
                        "is_invoice" => 1,
                        "status" => "delivered"
                    ));
                    DB::table("company_settings")->where("id", 1)->increment("invoice_no", 1);
                }
            }
        } catch (\Throwable $th) {
            return  redirect()->back()->with("error", $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
