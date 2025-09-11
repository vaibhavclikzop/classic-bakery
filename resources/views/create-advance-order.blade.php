@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Create Advance Order</h4>
            </div>
            <div class="">

                {{-- <a href="generate-po-product" class="btn btn-dark">Generate PO Via Products</a> --}}

            </div>
        </div>
        <div class="card-body">
            <form method="POST" id="frmMain" action="{{ route('SaveAdvanceOrder') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <label for="">Order Date</label>
                        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="form-control" required>

                    </div>
                    <div class="col-md-3">
                        <label for="">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="form-control"
                            required>

                    </div>
                    <div class="col-md-3">
                        <label for="">Delivery Time</label>
                        <input type="time" name="delivery_time" value="" class="form-control" required>

                    </div>
                    <div class="col-md-3">
                        <label for="">Customer Type</label>
                        <select name="customer_type" id="customer_type" class="form-control" required>
                            <option value="">Select</option>
                            <option value="outlet">Outlet</option>
                            <option value="customer">Customer</option>
                        </select>

                    </div>
                    <div class="col-md-3 mt-3">
                        <label for="">Shop/Customer</label>
                        <select name="outlet_id" id="outlet_id" class="form-control" required>
                            <option value="">Select</option>

                        </select>

                    </div>
                    <div class="col-md-3 mt-3 d-none">
                        <label for="">Order Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        <label for="">File</label> <br>
                                        <input type="file" name="files[]" class="form-control" multiple>
                                    </th>
                                    <th colspan="2">
                                        <label for="">Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                            {{-- @foreach ($adv_order_item_mst as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Flavour</label> <br>
                                        <select name="flavour_id" id="flavour_id" class="form-control">
                                            <option value="">Select Flavour</option>

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Weight</label> <br>
                                        <select name="weight" id="weight" class="form-control">
                                            <option value="">Select weight</option>
                                            @foreach ($adv_order_weight as $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Shape</label> <br>
                                        <select name="shape" id="shape" class="form-control">
                                            <option value="">Select Shape</option>
                                            @foreach ($adv_order_shape as $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                    </th>
                                    <th class="d-none">
                                        <label for="">Food Type</label> <br>
                                        <select name="food_type" id="food_type" class="form-control">
                                            <option value="">Select Shape</option>
                                            @foreach ($adv_order_food_type as $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="">
                                        <label for="">Customer Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Enter name on cake">
                                    </th>
                                    <th colspan="">
                                        <label for="">Message</label>
                                        <input type="text" name="message" id="message" class="form-control"
                                            placeholder="Enter message">
                                    </th>
                                    <th>
                                        <label for="">Description</label>
                                        <input type="text" name="description" id="description" class="form-control"
                                            placeholder="Enter description">
                                    </th>
                                    <th>
                                        <label for="">Discount %</label>
                                        <input type="number" step="0.01" name="discount_percentage"
                                            id="discount_percentage" value="0" class="form-control"
                                            placeholder="Enter Discount">
                                    </th>
                                    <th>
                                        <label for="">Discount ₹</label>
                                        <input type="number" step="0.01" class="form-control" name="discount_price"
                                            id="discount_price" value="0" placeholder="Enter Discount">
                                    </th>
                                    <th>
                                        <label for="">Qty</label>
                                        <input type="number" name="qty" id="qty" min="1"
                                            value="1" class="form-control" placeholder="Enter Qty">
                                    </th>


                                    <th>
                                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="">Margin in %</label>
                                        <input type="number" id="margin" class="form-control" disabled>
                                    </th>
                                    <th>
                                        <label for="">MRP per KG</label>
                                        <input type="number" id="mrp" class="form-control" disabled>
                                    </th>

                                    <th>
                                        <label for="">Increment Rate per KG</label>
                                        <input type="number" value="0" id="increment_rate" class="form-control"
                                            disabled>
                                    </th>
                                    <th>
                                        <label for="">Sale Price</label>
                                        <input type="number" value="0" id="price" class="form-control"
                                            disabled>
                                    </th>
                                </tr>

                            </thead>

                        </table>
                        <table class="table mt-3">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Item </th>
                                    <th>Flavour</th>
                                    <th>Shape</th>
                                    {{-- <th>Food Type</th> --}}
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Description</th>
                                    <th>Weight</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>

                            </thead>
                            <tbody id="prodList">

                            </tbody>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">

                        <div class="text-center col-md-12 mt-3">

                            <button type="button" id="SavePO" name="btnSubmit"
                                class="btn btn-warning">Submit</button>

                        </div>


                    </div>

                </div>

            </form>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#outlet_id").select2();
            $("#product_id").select2();
            $("#product_id").on("change", function() {
                var margin = $(this).find(":selected").data("margin");

                $("#margin").val(margin);
                $.ajax({
                    url: "/GetFlavourDetailItem",
                    type: "POST",
                    data: {
                        id: $(this).val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        var html = "";
                        html += '<option value="">----Select Flavour----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id +
                                '" data-fix_rate="' + element.fix_rate +
                                '" data-increment_rate="' + element.increment_rate +
                                '">' + element
                                .flavour +
                                '</option>';
                        });



                        $("#flavour_id").html(html)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });
            $("#flavour_id").on("change", function() {
                const fix_rate = $(this).find(":selected").data("fix_rate");
                const increment_rate = $(this).find(":selected").data("increment_rate")
                $("#mrp").val(fix_rate)
                $("#increment_rate").val(increment_rate)
                $("#price").val(0)
                $("#weight").val("")
            });
            var price = 0;
            $("#weight").on("change", function() {
                const weight = $(this).val();
                const mrp = $("#mrp").val();
                const increment_rate = $("#increment_rate").val()
                const qty = $("#qty").val();
                const margin = $("#margin").val();

                if (parseFloat(weight) > 1) {
                    const extra_weight = parseFloat(weight - 1);
                    const increment_price = (increment_rate * extra_weight)
                    const net_weight = weight - extra_weight;
                    const net_price = net_weight * mrp
                    price = (increment_price + net_price * qty)
                    price =price-(price/100*margin)
                    $("#price").val(price)
                } else {
                    price = (mrp * weight) * qty;
                    price =price-(price/100*margin)
                    $("#price").val(price)
                }

            });

            $("#qty").on("keyup", function() {
                var qty = $("#qty").val()
                let discount_price = $("#discount_price").val()
                new_price = (price - discount_price) * qty

                $("#price").val(new_price)

            });

            var product_list = [];
            var sno = 1;
            $("#addProduct").on("click", function() {
                var product_id = parseInt($("#product_id").val())
                var product_name = $("#product_id").find(":selected").text()
                var flavour_id = parseInt($("#flavour_id").val())
                var flavour_name = $("#flavour_id").find(":selected").text()
                var qty = parseInt($("#qty").val())
                var weight = ($("#weight").val())
                var shape = ($("#shape").val())
                var food_type = ($("#food_type").val())
                var name = ($("#name").val())
                var message = ($("#message").val())
                var description = ($("#description").val())
                var discount_price = ($("#discount_price").val())
                const price = ($("#price").val())

                var files = $("input[name='files[]']")[0].files; // Get selected files
                var fileArray = Array.from(files).map(file => file.name);


                if (!product_id || isNaN(product_id)) {
                    toastr.error("Select a valid Product");
                    return;
                }

                if (!qty || isNaN(qty) || qty <= 0) {
                    toastr.error("Enter a valid quantity");
                    return;
                }



                let existingProduct = product_list.find(product => product.flavour_id === flavour_id);
                if (existingProduct) {
                    toastr.error("Flavour already exists");
                    return;
                }

                var html = `<tr class="product${flavour_id}">
                            <td>${sno++}</td>    
                            <td>${product_name}</td>    
                            <td>${flavour_name}</td>    
                            <td>${shape}</td>    
                            <td class="d-none">${food_type}</td>    
                            <td>${name}</td>    
                            <td>${message}</td>    
                            <td>${description}</td>    
                            <td>${weight}</td>    
                            <td>${qty}</td>    
                            <td>${price}</td>    
                     
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${flavour_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

                $("#prodList").append(html)
                product_list.push({
                    flavour_id,
                    product_id,
                    weight,
                    shape,
                    food_type,
                    name,
                    message,
                    qty,
                    files: fileArray,
                    description,
                    discount_price

                });

            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.flavour_id !== id);

            });
            $("#SavePO").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));
                if (!$("#outlet_id").val()) {
                    toastr.error("Select Outlet");
                    return;
                }


                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }
                $('#frmMain').submit()
                $("#SavePO").attr("disabled", "disabled")
            });
            $("#discount_percentage").on("keyup", function() {
                let percentage = $(this).val();
                let qty = $("#qty").val();
                let discount_price = price / 100 * percentage;
                let new_price = price - discount_price;
                $("#discount_price").val(discount_price)
                $("#price").val(new_price * qty)
            });

            $("#discount_price").on("keyup", function() {
                let discount_price = parseFloat($(this).val())
                let qty = parseInt($("#qty").val())


                if (price > 0) {
                    let discount_percentage = (((price - discount_price) / price) * 100);
                    let new_price = price - discount_price;

                    $("#discount_percentage").val(100 - discount_percentage);
                    $("#price").val((new_price * qty).toFixed(2));
                }
            });
        });





        $("#customer_type").on("change", function() {
            $.ajax({
                url: "/GetCustomerOrOutlet",
                type: "POST",
                data: {
                    type: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element
                            .name +
                            '</option>';
                    });
                    $("#outlet_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });



        $("#outlet_id").on("change", function() {
            $.ajax({
                url: "/GetAdvProduct",
                type: "POST",
                data: {
                    id: $(this).val(),
                    customer_type: $("#customer_type").val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Product----</option>';
                    result.forEach(element => {


                        html += '<option value="' + element.id + '" data-margin="' + element
                            .margin + '">' + element
                            .name +
                            '</option>';
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
    </script>
@endsection
