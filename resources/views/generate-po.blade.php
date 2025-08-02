@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Generate PO</h4>


            </div>
            <div class="">

                {{-- <a href="generate-po-product" class="btn btn-dark">Generate PO Via Products</a> --}}

            </div>

        </div>
        <div class="card-body">
            <div class="alert alert-danger" role="alert" style="display: none">
                <strong id="vendor_name"> </strong>
            </div>

            <form method="POST" id="frmMain" action="{{ route('SavePO') }}">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select Vendor</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}" data-city="{{ $item->city }}">
                                    {{ $item->company_name }} ( {{ $item->name }})
                                </option>
                            @endforeach

                        </select>

                    </div>
                    <div class="col-md-3">
                        <label for="">PO Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter PO Name">

                    </div>
                    <div class="col-md-6">
                        <label for="">Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Enter PO Description">

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <label for="">Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Qty</label>
                                        <input type="number" name="qty" id="qty" min="1" value="1"
                                            class="form-control" placeholder="Enter Qty">
                                    </th>

                                    <th>
                                        <label for=""> Price</label>
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control" placeholder="Enter price">

                                    </th>

                                    <th>
                                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Cess</th>
                                    <th>GST</th>

                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">Total </th>
                                    <th id="subtotal"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">

                        <div class="text-center col-md-12 mt-3">

                            <button type="button" id="SavePO" name="btnSubmit" class="btn btn-warning">Submit</button>

                        </div>


                    </div>

                </div>

            </form>
        </div>

    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
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
                    <div class="mb-3">
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
            var product_list = [];
            var sno = 1;

            $("#vendor_id").select2();
            $("#product_id").select2();
            $("#vendor_id").on("change", function() {
                product_list = [];
                $("#prodList").html("")
                $("#product_id").html("")

                var city = $(this).find(":selected").data("city")
                var company_city = "{{ $setting->city }}";

                if (city == false) {

                    $("#vendor_name").text(
                        "Vendor has no city selected. Please select a city before proceeding.")
                    $(".alert").show()
                    return;
                }
                if (company_city == false) {
                    $("#vendor_name").text(
                        "Company has no city selected. Please select a city before proceeding. Go to setting select city and save"
                    )
                    $(".alert").show()
                    return;
                } else {
                    $(".alert").hide()
                }
                $.ajax({
                    url: "/GetVendorProducts",
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
                        html += '<option value="">----Select Products----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '" data-price="' +
                                element
                                .price + '"   data-gst="' +
                                element
                                .gst + '" data-cess="' + element.cess_tax + '">' +
                                element.name +
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
            $("#product_id").on("change", function() {
                var product_price = $(this).find(":selected").data("price");

                $.ajax({
                    url: "/getLastPurchasePriceRM",
                    type: "POST",
                    data: {
                        product_id: $(this).val(),
                        vendor_id: $("#vendor_id").val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        $("#product_price").val(product_price)
                        $("#price").val(result)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });


            $("#addProduct").on("click", function() {
                var product_id = parseInt($("#product_id").val())
                var product_name = $("#product_id").find(":selected").text()
                var qty = parseFloat($("#qty").val())
                var price = parseFloat($("#price").val())
                var gst = $("#product_id").find(":selected").data("gst")
                var cess = $("#product_id").find(":selected").data("cess")


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
                    toastr.error("Product already exists");
                    return;
                }

                var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td>${product_name}</td>    
                            <td>${qty}</td>    
                            <td>${price}</td>    
                            <td>${cess}</td>   
                            <td>${gst}</td>    
                            <td>${ parseFloat( (price*qty)+price*qty/100*gst+(price*qty/100*cess)).toFixed(5)}</td>   
                            <td> 

                                <button type="button"  class="btn btn-primary edit btn-sm"  data-id="${product_id}" data-price="${price}" data-qty="${qty}">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>

                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

                $("#prodList").append(html)
                product_list.push({
                    product_id,
                    qty,
                    price,
                    gst,
                    cess
                });
                calculate_total(product_list);
                $("#qty").val("")
                $("#price").val("")
                $("#product_id").val(null).trigger("change");


            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.product_id !== id);
                calculate_total(product_list)

            });
            $("#SavePO").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));
                if (!$("#vendor_id").val()) {
                    toastr.error("Select Vendor");
                    return;
                }

                if (!$("#name").val()) {
                    toastr.error("Enter PO name");
                    return;
                }

                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }


                if ($("#password").val() == false) {
                    toastr.error("Enter Password");
                    return;
                }
                $('#frmMain').submit()

            })


            function calculate_total(product_list) {
                let total = 0;

                product_list.forEach(item => {
                    const base_total = item.qty * item.price;
                    const gst_amount = item.gst ? (base_total * item.gst / 100) : 0;
                    const cess_amount = item.cess ? (base_total * item.cess / 100) : 0;
                    total += base_total + gst_amount + cess_amount;
                });

                $("#subtotal").text(parseFloat(total).toFixed(5));


            }


            let currentEditId = null;

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
                const gst = row.find("td").eq(5).text();
                const cess = row.find("td").eq(4).text();
                const total = (newPrice * newQty) + ((newPrice * newQty * parseFloat(gst)) / 100);

                row.html(`
                    <td>${row.index() + 1}</td>
                    <td>${product_name}</td>
                    <td>${newQty}</td>
                    <td>${newPrice}</td>
                    <td>${cess}</td>
                    <td>${gst}</td>
                    <td>${total.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-primary edit btn-sm" data-id="${id}" data-price="${newPrice}" data-qty="${newQty}">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-danger remove btn-sm" data-id="${id}">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                `);

                // Update in the array
                product_list = product_list.map(item => {
                    if (item.product_id === id) {
                        return {
                            ...item,
                            qty: newQty,
                            price: newPrice
                        };
                    }
                    return item;
                });

                calculate_total(product_list);
                $('#editProductModal').modal('hide');
            });
            $(window).on("pageshow", function(event) {
                if (event.originalEvent.persisted) {
                    // Browser back button used
                    $("#frmMain")[0].reset();
                    product_list = [];
                    $("#prodList").html("");
                    $("#subtotal").text("");
                    $("#prodList").val("");
                    $("#po_id").val("")
                }
            });
        });
 
    $(document).ready(function () {
        // Bind keydown event on all relevant inputs
        $('#product_id, #qty, #price').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();  
                $('#addProduct').click(); 
            }
        });
    });
</script>
@endsection
