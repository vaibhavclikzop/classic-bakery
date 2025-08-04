@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Create Outward Challan </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Create Outward Challan</h4>
            </div>

        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" id="UploadForm" novalidate
                action="{{ route('SaveCustomerOutward') }}">
                @csrf

                <div class="row">
                    <div class="col-md-3 mt-3">
                        <label for="">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            @foreach ($customer as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-3 mt-3">
                        <label for="">Order</label>
                        <select name="order_id" id="order_id" class="form-control" required>
                            <option value="">Select Order</option>

                        </select>

                    </div>



                    <div class="col-md-3 mt-3">
                        <label for="">Outward Challan Date</label>
                        <input type="date" id="invoice_date" name="invoice_date" value="{{ date('Y-m-d') }}"
                            class="form-control" required>


                    </div>
                    <div class="col-md-3 mt-3">
                        <label for="">Mode of Transport</label>
                        <select name="mode_of_transport" id="mode_of_transport" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($mode_of_transport as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>


                    </div>



                    <div class="col-md-12 mt-3">
                        <label for="">Description</label>
                        <input type="" id="description" name="description" class="form-control">


                    </div>


                    <div class="col-md-12">
                        <hr>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        <label for="">Category</label> <br>
                                        <select name="category_id" id="category_id">
                                            <option value="">Select</option>
                                            @foreach ($f_product_category as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th colspan="3">
                                        <label for="">Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Order Qty</label>
                                        <input class="form-control" id="stock" disabled>
                                    </th>
                                    <th>
                                        <label for="">Qty</label>
                                        <input type="number" name="qty" id="qty" min="1" value="1"
                                            class="form-control" placeholder="Enter Qty">
                                    </th>


                                    <th>
                                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th colspan="3">Product </th>
                                    <th>Current Stock </th>
                                    <th>Ordered Qty </th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>

                        </table>
                        <input type="hidden" id="prod_list" name="prod_list">

                    </div>
                    <div class="col-md-12 mt-5 text-center">
                        <button class="btn btn-success" id="btnSubmit" type="submit">Save</button>

                    </div>
                </div>
            </form>
        </div>

    </div>


    <script>
        $(document).ready(function() {
            $("#customer_id").select2();
            $("#order_id").select2();
        })

        var customer_id = {{ request('customer_id') }}
        var order_id = {{ request('id') }}
        var price = "";
        var location_id = "";
        var product_id = "";
        var product_list = [];
        var sno = 1;



        $("#customer_id").on("change", function() {
            price = "";
            location_id = "";
            product_id = "";
            product_list = [];
            sno = 1;

            $("#prodList").html("")
            var id = customer_id
            if ($(this).val()) {
                id = $(this).val();
                customer_id = id;
            }
            $.ajax({
                url: "/GetCustomerOrder",
                type: "POST",
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Order----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.order_id +
                            '</option>';
                    });
                    $("#order_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });


        });

        $("#category_id").on("change", function() {


            var category_id = $(this).val()
            var customer_id = $("#customer_id").val()
            $.ajax({
                url: "/GetCustomerTypeProducts",
                type: "POST",
                data: {
                    id: category_id,
                    customer_id: customer_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Products----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '" data-price="' + element
                            .sale_price + '"  data-gst="' + element.gst + '"   >' + element
                            .name +
                            ' (Stock :' + element.stock + ')</option>';
                    });
                    $("#product_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });

        $("#order_id").on("change", function() {

            if ($(this).val()) {
                var id = $(this).val();
            } else {
                var id = order_id;
            }
            $.ajax({
                url: "/GetCustomerOrderProduct",
                type: "POST",
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    price = "";
                    location_id = "";
                    product_id = "";
                    product_list = [];
                    sno = 1;

                    $("#prodList").html("")


                    cs_stock = 0;
                    result.forEach(element => {
                        var color = "bg-soft-success";

                        var product_id = element.product_id;
                        var qty = element.qty - element.booked_qty;
                        var stock = element.stock;
                        if (element.booked_qty < element.qty) {


                            if (qty > stock) {
                                qty = stock
                                color = "bg-soft-danger";
                            }

                            if (stock > 0 && cs_stock == 0) {
                                cs_stock = 1;
                            }
                            var html = `<tr class="product${element.product_id}" style="color:green">
                            <td class="${color}">${sno++}</td>    
                            <td colspan="3" class="${color}">${element.name}</td>    
                            <td colspan="" class="${color}">${element.stock}</td>    
                            <td class="${color}"> ${element.qty}</td>    
                            <td class="${color}">
                                <input type="number" step="" class="form-control qty"  data-product_id="${product_id}"  value="${qty}"  data-actual_qty="${qty}" data-stock="${stock}">
                            </td>    
                           
                
                            <td class="${color}"> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${element.product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

                            $("#prodList").append(html)
                            product_list.push({
                                product_id,
                                qty,

                            });
                        }

                    });

                    $("#customer_id").val(customer_id)
                    setTimeout(() => {
                        $("#order_id").val(id)
                    }, 1000);
                    if (cs_stock == 0) {
                        $("#btnSubmit").attr("disabled", "disabled")
                    } else {
                        $("#btnSubmit").removeAttr("disabled")
                    }

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });
        $("#product_id").on("change", function() {

            var stock = $(this).find(":selected").data("qty");
            $("#stock").val(stock)



        });
        $(document).ready(function() {
            $("#product_id").select2();
        })



        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var qty = parseInt($("#qty").val())
            var stock = parseInt($("#product_id").find(":selected").data("qty"))


            if (!product_id || isNaN(product_id)) {
                toastr.error("Select a valid Product");
                return;
            }

            if (!qty || isNaN(qty) || qty <= 0) {
                toastr.error("Enter a valid quantity");
                return;
            }




            let existingProduct = product_list.find(product => product.product_id === product_id);
            if (existingProduct) {
                toastr.error("Product already exists");
                return;
            }

            var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td colspan="3">${product_name}</td>    
                            <td>${stock}</td>    
                            <td>${qty}</td>    
                           
                
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

            $("#prodList").append(html)
            product_list.push({
                product_id,
                qty,

            });

        });



        $(document).on("keyup", '.qty', function() {
            var product_id = parseInt($(this).data("product_id"))

            var qty = parseInt($(this).val());
            var stock = parseInt($(this).data("stock"));

            var actual_qty = parseInt($(this).data("actual_qty"))

            if (qty > stock) {
                toastr.error("You don't have stock");
                $(this).val(stock)
                return;
            }
            if (qty <= 0) {
                toastr.error("Qty can not be zero or less then zero");
                $(this).val(qty)
                return;
            }

            var product = product_list.find(item => item.product_id === product_id);

            if (product) {

                product.qty = qty;
                console.log("Updated Product List:", product_list);
            } else {
                toastr.error("Something went wrong");
                return;
            }



        })


        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });


        $("#UploadForm").on("submit", function() {

            if ($("#mode_of_transport").val()==false) {
                    toastr.error("Please select mode of transport");
                    return;
            }

            $('#prod_list').val(JSON.stringify(product_list));  
            $("#btnSubmit").attr("disabled", "disabled")

        });
        $("#customer_id").trigger("change");
        $("#order_id").trigger("change");
    </script>
@endsection
