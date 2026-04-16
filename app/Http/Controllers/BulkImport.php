<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class BulkImport extends Controller
{
    function generateRandomNumber($length = 12)
    {
        $number = '';
        while (strlen($number) < $length) {
            $number .= mt_rand(0, 9);
        }
        return substr($number, 0, $length);
    }

    public function ImportProducts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $brand_id = "";
                $category_id = "";
                $sub_category_id = "";
                $unit_type_id = "";
                try {

                    $brand = DB::table("brand")->where("name", $record[0])->first();
                    if ($brand) {
                        $brand_id = $brand->id;
                    } else {
                        $brand_id =  DB::table('brand')->insertGetId(array(
                            "name" => $record[0],

                        ));
                    }

                    $category = DB::table("category")->where("name", $record[1])->first();
                    if ($category) {
                        $category_id = $category->id;
                    } else {
                        $category_id =  DB::table('category')->insertGetId(array(
                            "name" => $record[1],
                            "brand_id" => $brand_id,

                        ));
                    }
                    $sub_category = DB::table("sub_category")->where("name", $record[2])->first();
                    if ($sub_category) {
                        $sub_category_id = $sub_category->id;
                    } else {
                        $sub_category_id =  DB::table('sub_category')->insertGetId(array(
                            "name" => $record[2],
                            "category_id" => $category_id,

                        ));
                    }

                    $unit_type = DB::table("unit_type")->where("name", $record[7])->first();
                    if ($unit_type) {
                        $unit_type_id = $unit_type->id;
                    } else {
                        $unit_type_id =  DB::table('unit_type')->insertGetId(array(
                            "name" => $record[7],
                        ));
                    }


                    $gst = DB::table("gst")->where("gst", $record[10])->first();
                    if (!$gst) {
                        DB::table('gst')->insertGetId(array(
                            "gst" => $record[10],
                        ));
                    }


                    $products = DB::table("products")->where("article_no", $record[4])->first();
                    if ($products) {
                        $error .= "Raw ID " . $count . " Duplicate article no. <br>";
                        $duplicate++;
                    } else {
                        $barcode = $this->generateRandomNumber(10);


                        $product =  DB::table('products')->insertGetId(array(
                            "brand_id" => $brand_id,
                            "category_id" => $category_id,
                            "sub_category_id" => $sub_category_id,
                            "name" => $record[3],
                            "article_no" => $record[4],
                            "price" => $record[5],
                            "min_stock" => $record[6],
                            "uom" => $unit_type_id,
                            "active" => 1,
                            "bar_code" => $barcode,
                            "hsn_code" => $record[8],
                            "warranty_days" => $record[9],
                            "gst" => $record[10],

                        ));
                        $success++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count . " Invalid format. <br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }







    public function ImportFinishProducts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {

                $category_id = "";
                $sub_category_id = "";
                $unit_type_id = "";
                try {
                    if ($record[2]=="name") {
                     continue;
                    }


                    $category = DB::table("f_product_category")->where("name", $record[0])->first();
                    if ($category) {
                        $category_id = $category->id;
                    } else {
                        $category_id =  DB::table('f_product_category')->insertGetId(array(
                            "name" => $record[0],


                        ));
                    }
                    $sub_category = DB::table("f_product_sub_category")->where("name", $record[1])->first();
                    if ($sub_category) {
                        $sub_category_id = $sub_category->id;
                    } else {
                        $sub_category_id =  DB::table('f_product_sub_category')->insertGetId(array(
                            "name" => $record[1],
                            "f_category_id" => $category_id,

                        ));
                    }

                    $unit_type = DB::table("unit_type")->where("name", $record[6])->first();
                    if ($unit_type) {
                        $unit_type_id = $unit_type->id;
                    } else {
                        $unit_type_id =  DB::table('unit_type')->insertGetId(array(
                            "name" => $record[6],
                        ));
                    }



                    $products = DB::table("finish_products_mst")
                        ->where("f_category_id", $category_id)
                        ->where("f_sub_category_id", $sub_category_id)
                        ->where("name", trim($record[2]))
                        ->where("price", (float) trim($record[4]))
                        ->first();



                    if ($products) {
                        $error .= "Raw ID " . $count . " Duplicate product. <br>";

                        $duplicate++;
                    } else {
                        $barcode = $this->generateRandomNumber(10);

                        $articleNo = strtoupper(substr(trim($record[2]), 0, 3));



                        $fpmCount = DB::table("finish_products_mst")->count();

                        $newArticleNo= substr(trim($articleNo), 0, 3) . $fpmCount+1 . "<br>";

                        DB::table('finish_products_mst')->insertGetId(array(

                            "f_category_id" => $category_id,
                            "f_sub_category_id" => $sub_category_id,


                            "name" => $record[2],
                            "article_no" => $newArticleNo,
                            "price" => $record[4],
                            "min_stock" => $record[5],
                            "uom" => $unit_type_id,

                            "active" => 1,
                            "bar_code" => $barcode,
                            "hsn_code" => $record[7],
                            "gst" => $record[8],
                            "warranty_days" => $record[9],

                        ));
                        $success++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count .  $th->getMessage()."<br>";
                    $error_count++;
                }
                $count++;
            }

       
            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }


    public function ImportVendor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $state = "";
                $city = "";

                try {

                    $state_city = DB::table("state_city")->where("state", $record[6])->orWhere("city", $record[7])->first();
                    if ($state_city) {
                        $state = $state_city->state;
                        $city = $state_city->city;
                    }


                    if (strlen($record[2]) == 10 && is_numeric($record[2])) {


                        $vendor = DB::table("vendor")->where("number", $record[2])->first();
                        if ($vendor) {
                            $error .= "Raw ID " . $count . " Duplicate Vendor no. <br>";
                            $duplicate++;
                        } else {

                            DB::table('vendor')->insertGetId(array(
                                "company_name" => $record[0],
                                "name" => $record[1],
                                "number" => $record[2],
                                "email" => $record[3],
                                "gst" => $record[4],
                                "address" => $record[5],
                                "state" => $state,
                                "city" => $city,
                                "pincode" => $record[8],
                            ));
                            $success++;
                        }
                    } else {
                        $error .= "Raw ID " . $count . " Invalid format. <br>";
                        $error_count++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count . " Invalid format. " . $th->getMessage() . "<br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }


    public function ImportCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $state = "";
                $city = "";

                $ship_state = "";
                $ship_city = "";
                try {

                    $state_city = DB::table("state_city")->where("state", $record[9])->orWhere("city", $record[10])->first();
                    if ($state_city) {
                        $state = $state_city->state;
                        $city = $state_city->city;
                    }

                    $state_city = DB::table("state_city")->where("state", $record[15])->orWhere("city", $record[16])->first();
                    if ($state_city) {
                        $ship_state = $state_city->state;
                        $ship_city = $state_city->city;
                    }

                    $customer_type =  DB::table("customer_type")->where("name", $record[0])->first();
                    if (!$customer_type) {
                        $error .= " Raw ID " . $count . " Customer Type Not Found. <br>";
                        $error_count++;
                        $count++;
                        continue;
                    }

                    if (strlen($record[4]) == 10 && is_numeric($record[4])) {

                        $customers = DB::table("customers")->where("number", $record[4])->first();
                        if ($customers) {
                            $error .= "Raw ID " . $count . " Duplicate Customer no. <br>";
                            $duplicate++;
                        } else {

                            DB::table('customers')->insertGetId(array(
                                "customer_type_id" => $customer_type->id,
                                "company" => $record[1],
                                "name" => $record[2],
                                "nickname" => $record[3],
                                "number" => $record[4],
                                "email" => $record[5],
                                "gst" => $record[6],
                                "fssai_no" => $record[7],
                                "address" => $record[8],
                                "state" => $state,
                                "city" => $city,
                                "pincode" => $record[11],

                                "ship_gst" => $record[12],
                                "ship_fssai_no" => $record[13],
                                "ship_address" => $record[14],
                                "ship_state" => $ship_state,
                                "ship_city" => $ship_city,
                                "ship_pincode" => $record[17],

                            ));
                            $success++;
                        }
                    } else {
                        $error .= "Raw ID " . $count . " Invalid Number. <br>";
                        $error_count++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count . " Invalid format. " . $th->getMessage() . "<br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }
}
