@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Create Order </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Create Order</h4>
            </div>

        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" id="UploadForm" novalidate action="{{ route('SaveOrder') }}">
                @csrf

                <div class="row">
                    <div class="col-md-2 mt-3">
                        <label for="">Customer Type</label>
                        <select name="order_type" id="order_type" class="form-control" required>
                            <option value="">Select</option>
                            <option value="customer">Customer</option>
                            <option value="outlet">Outlet</option>

                        </select>

                    </div>
                    <div class="col-md-3 mt-3">
                        <label for="">Customer / Outlet</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select customer</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-2 mt-3">
                        <label for="">Order Date</label>
                        <input type="date" id="order_date" name="order_date" value="{{ date('Y-m-d') }}"
                            class="form-control">


                    </div>
                    <div class="col-md-3 mt-3">
                        @php
                            $now_days = date('l');
                        @endphp
                        <label for="">Select Order Type</label>
                        <select name="order_type_id" id="order_type_id">

                            <option value="">Select</option>
                            @foreach ($order_type as $item)
                                @php
                                    $disabled = '';
                                    $orderDaysArray = explode(', ', $item->week_days); // Convert string to array
                                    if (!in_array($now_days, $orderDaysArray)) {
                                        $disabled = 'disabled';
                                    }
                                @endphp
                                <option value="{{ $item->id }}" data-days="{{ $item->days }}" {{ $disabled }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mt-3">
                        <label for="">Expected Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control">


                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="">Description</label>
                        <input type="" id="description" name="description" class="form-control">


                    </div>
                    <hr>

                    <div class="col-md-3 mt-3">
                        <label for="">Product</label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="">Select product</option>

                        </select>

                    </div>
                    <div class="col-md-2 mt-3">
                        <label for="">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" disabled>

                    </div>
                    <div class="col-md-2 mt-3">
                        <label for="">Qty</label>
                        <input type="number" class="form-control" id="qty" name="qty">

                    </div>
                    <div class="col-md-1 mt-3">
                        <label for="">Discount</label>
                        <input type="number" step="0.01" class="form-control" id="discount" name="discount"
                            value="0" required>

                    </div>

                    <div class="col-md-1 mt-3">

                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>

                    </div>

                    <div class="col-md-12 mt-2">
                        <hr>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product </th>

                                    <th>MRP</th>
                                    <th>Qty</th>
                                    <th>Price </th>
                                    <th>Discount </th>
                                    <th>Rate </th>
                                    <th>Taxable Amt.</th>
                                    <th>GST</th>
                                    <th>Total</th>

                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                            <tbody id="prodFooter">
                                <tr>
                                    <th colspan="9" style="text-align: right">Total</th>
                                    <th id="footerTotal" colspan="2"></th>
                                </tr>
                            </tbody>

                        </table>
                        <input type="hidden" id="prod_list" name="prod_list">

                    </div>
                    <div class="col-md-12 mt-5 text-center">
                        <button class="btn btn-success" id="btnSubmit" type="submit">Save Order</button>

                    </div>
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_product_id">
                    <div class="mb-3">
                        <label for="edit_qty" class="form-label">Quantity</label>
                        <input type="number" min="1" class="form-control" id="edit_qty">
                    </div>
                    <div class="mb-3 d-none">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="updateProduct" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            $("select").select2();
        })
        var price = "";
        var location_id = "";
        var product_id = "";
        var product_list = [];
        var sno = 1;
        $("#order_type").on("change", function() {
            $.ajax({
                url: "/GetCustomerOutletList",
                type: "POST",
                data: {
                    order_type: $(this).val(),
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

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#customer_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        })

        $("#product_id").on("change", function() {


            $("#price").val($(this).find(":selected").data("price"))


        });

        $("#customer_id").on("change", function() {
            price = "";
            location_id = "";
            product_id = "";
            product_list = [];
            sno = 1;
            $("#prodList").html("")
        })
        $("#order_type_id").on("change", function() {


            price = "";
            location_id = "";
            product_id = "";
            product_list = [];
            sno = 1;
            $("#prodList").html("")

            var order_type_id = $(this).val()
            var customer_id = $("#customer_id").val()
            var order_type = $("#order_type").val()


            let daysToAdd = $(this).find(":selected").data("days")

            let currentDate = new Date();
            currentDate.setDate(currentDate.getDate() + daysToAdd);

            // Format the date as YYYY-MM-DD for input fields
            let formattedDate = currentDate.toISOString().split('T')[0];

            $("#delivery_date").val(formattedDate);
            $.ajax({
                url: "/GetCustomerTypeProducts",
                type: "POST",
                data: {
                    order_type_id: order_type_id,
                    customer_id: customer_id,
                    order_type: order_type,
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
                            .sale_price +
                            '"  data-gst="' + element.gst + '" data-mrp="' + element.mrp +
                            '"   >' + element.name +
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

            $("#prodList").html("")
            product_list = [];
            console.log(product_list);

        });







        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var qty = parseFloat($("#qty").val())
            var price = parseFloat($("#price").val())
            var gst = $("#product_id").find(":selected").data("gst");
            var gst_type = "Outer GST";
            let discount = parseFloat($("#discount").val());
            let rate = parseFloat(price - (price / 100 * discount)).toFixed(2);
            let gst_amount = parseFloat(rate - (rate / (1 + gst / 100))).toFixed(2);
            let taxable = parseFloat(rate - gst_amount).toFixed(2);
            let mrp = $("#product_id").find(":selected").data("mrp");

            if (!product_id || isNaN(product_id)) {
                toastr.error("Select a valid Product");
                return;
            }

            if (!qty || isNaN(qty) || qty <= 0) {
                toastr.error("Enter a valid quantity");
                return;
            }

            if (!price || isNaN(price) || price <= 0) {
                toastr.error("Enter a valid price");
                return;
            }

            let existingProduct = product_list.find(product => product.product_id === product_id);
            if (existingProduct) {
                var product_id = product_id

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {
                    qty = product.qty + qty


                    $(`.product${product_id}`).find('td:eq(2)').text(qty);


                    product.qty = qty;
                    console.log(product_list);

                } else {
                    toastr.error("Something went wrong");
                    return;
                }

                return;
            }

            var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td>${product_name}</td>    
                            <td>${mrp}</td>    
                            <td>${qty}</td>    
                            <td>${price}</td>    
                            <td>${discount} </td>    
                            <td>${rate}</td>
                            <td>${taxable}</td>    

                            <td>${gst} % <br> ${gst_amount} ₹ </td>    
                              <td>${rate*qty}</td>    
                          
                            <td> 
                                  <button type="button" class="btn btn-primary edit btn-sm" data-id="${product_id}" data-price="${price}" data-qty="${qty}">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

            $("#prodList").append(html)
            let total = parseFloat(rate * qty).toFixed(2);
            product_list.push({
                product_id,
                qty,
                price,
                gst,
                gst_type,
                discount,
                total,
            });

            $("#qty").val("")
            $("#price").val("")
            $("#product_id").val(null).trigger("change");
            footerUpdate();

        });
        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);
            footerUpdate();

        });
        $("#UploadForm").on("submit", function(e) {


            $('#prod_list').val(JSON.stringify(product_list));
            if (product_list.length === 0) {
                e.preventDefault();
                return toastr.error("Add at least one product.");
            }
            $("#btnSubmit").attr("disabled", true);
        });
        $(window).on("pageshow", function(event) {
            if (event.originalEvent.persisted) {
                // Browser back button used
                $("#formMain")[0].reset();
                product_list = [];
                $("#productList").html("");
                $("#subtotal").text("");
                $("#productList").val("");
                $("#po_id").val("")
            }
        });


        $(document).on("click", ".edit", function() {
            const id = $(this).data("id");
            const price = $(this).data("price");
            const qty = $(this).data("qty");

            currentEditId = id;
            $("#edit_product_id").val(id);
            $("#edit_qty").val(qty);
            $("#edit_price").val(price);

            $('#editProductModal').modal('show');
        });


        $("#updateProduct").on("click", function() {
            const id = parseInt($("#edit_product_id").val());
            const newQty = parseFloat($("#edit_qty").val());
            const newPrice = parseFloat($("#edit_price").val());

            if (!newQty || newQty <= 0 || !newPrice || newPrice <= 0) {
                toastr.error("Enter valid quantity and price");
                return;
            }

            // Update the row
            let row = $(`.product${id}`);
            const product_name = row.find("td").eq(1).text();

            row.find("td").eq(0).text(row.index() + 1); // Sr No
            row.find("td").eq(3).text(newQty);


            // Update in the array
            product_list = product_list.map(item => {
                if (item.product_id === id) {
                    return {
                        ...item,
                        qty: newQty,
                        price: newPrice,
                        total: newQty * newPrice
                    };
                }
                return item;
            });

            console.log(product_list);
            $('#editProductModal').modal('hide');
            footerUpdate();
        });

        function footerUpdate() {
            let total = 0;

            product_list.forEach(element => {
                total += parseFloat(element.total) || 0;
            });

            $("#footerTotal").text(total.toFixed(2));
        }

        $(document).ready(function() {
            // Bind keydown event on all relevant inputs
            // $('#product_id, #qty, #price').on('keydown', function(e) {
            //     if (e.key === 'Enter') {
            //         e.preventDefault();
            //         $('#addProduct').click();
            //     }
            // });


            let order = [

                '#product_id',

                '#qty',
                '#discount',
                '#addProduct'
            ];

            function focusNext(current) {
                let index = order.indexOf(current);
                if (index !== -1 && index + 1 < order.length) {
                    let nextField = order[index + 1];

                    // If next is Select2 → open dropdown
                    if ($(nextField).hasClass('select2-hidden-accessible')) {
                        $(nextField).select2('open');
                    } else {
                        $(nextField).focus();
                    }
                }
            }

            // Normal inputs
            $(document).on('keydown', 'input', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    focusNext('#' + $(this).attr('id'));
                }
            });



            // Select2 product_id → move to description
            $('#product_id').on('select2:select', function() {
                focusNext('#product_id');
            });

            // When Enter on Add Button
            $('#addProduct').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    $('#addProduct').click();

                    // Return to customer_id
                    $('#product_id').select2('open');
                }
            });
        });
    </script>
@endsection
