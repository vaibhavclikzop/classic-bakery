@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Generate PO</h4>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" id="frmMain" action="{{ route('SaveFinishPO') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select Vendor</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}">{{ $item->company_name }} ({{ $item->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>PO Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter PO Name">
                    </div>

                    <div class="col-md-6">
                        <label>Description</label>
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
                                    <th colspan="3">
                                        <label>Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                        </select>
                                    </th>
                                    <th><label>Qty</label><input type="number" name="qty" id="qty" min="1"
                                            value="1" class="form-control"></th>
                                    <th><label>Price</label><input type="number" step="0.01" name="price"
                                            id="price" class="form-control"></th>
                                    <th><button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>GST</th>
                                    <th>Cess Tax</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">Total</th>
                                    <th id="subtotal"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list">
                        <div class="text-center col-md-12 mt-3">
                            <button type="button" id="SavePO" name="btnSubmit" class="btn btn-warning">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_product_id">
                    <div class="mb-3">
                        <label for="edit_qty">Quantity</label>
                        <input type="number" min="1" class="form-control" id="edit_qty">
                    </div>
                    <div class="mb-3">
                        <label for="edit_price">Price</label>
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
        $(function() {
            $("#product_id, #vendor_id").select2();

            let product_list = [],
                sno = 1,
                currentEditId = null;

            function populateProductDropdown(data) {
                let html = '<option value="">----Select Products----</option>';
                data.forEach(element => {
                    html +=
                        `<option value="${element.id}" data-price="${element.price}" data-gst="${element.gst}"  data-cess_tax="${element.cess_tax}">${element.name}</option>`;
                });
                $("#product_id").html(html);
            }

            $("#vendor_id").on("change", function() {
                product_list = [];
                $("#prodList").html("")
                $.ajax({
                    url: "/GetVendorFinishProducts",
                    type: "POST",
                    data: {
                        id: $(this).val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: () => $("#loader").show(),
                    success: result => populateProductDropdown(result),
                    complete: () => $("#loader").hide(),
                    error: result => toastr.error(result.responseJSON.message)
                });
            });

            $("#product_id").on("change", function() {
                var product_price = $(this).find(":selected").data("price");

                $.ajax({
                    url: "/getLastPurchasePriceFG",
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
                const product_id = parseInt($("#product_id").val());
                const product_name = $("#product_id option:selected").text();
                const cess_tax = $("#product_id option:selected").data("cess_tax");
                const qty = parseFloat($("#qty").val());
                const price = parseFloat($("#price").val());
                const gst = parseFloat($("#product_id option:selected").data("gst"));

                if (!product_id || isNaN(qty) || qty <= 0 || isNaN(price) || price <= 0) {
                    toastr.error("Please enter valid product, quantity, and price.");
                    return;
                }

                if (product_list.some(p => p.product_id === product_id)) {
                    toastr.error("Product already added.");
                    return;
                }

                const total = (price * qty) + ((price * qty * gst) / 100);
                $("#prodList").append(`
                    <tr class="product${product_id}">
                        <td>${sno++}</td>
                        <td>${product_name}</td>
                        <td>${qty}</td>
                        <td>${price}</td>
                        <td>${gst}</td>
                        <td>${cess_tax}</td>
                        <td>${total.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-primary edit btn-sm" data-id="${product_id}" data-price="${price}" data-qty="${qty}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-danger remove btn-sm" data-id="${product_id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);

                product_list.push({
                    product_id,
                    qty,
                    price,
                    gst,
                    cess_tax
                });
                calculate_total();
                $("#qty, #price").val('');
                $("#product_id").val(null).trigger("change");
            });

            $(document).on("click", ".remove", function() {
                const id = parseInt($(this).data("id"));
                product_list = product_list.filter(p => p.product_id !== id);
                $(`.product${id}`).remove();
                calculate_total();
            });

            $(document).on("click", ".edit", function() {
                const id = $(this).data("id");
                const qty = $(this).data("qty");
                const price = $(this).data("price");

                $("#edit_product_id").val(id);
                $("#edit_qty").val(qty);
                $("#edit_price").val(price);
                $("#editProductModal").modal('show');
            });

            $("#updateProduct").on("click", function() {
                const id = parseInt($("#edit_product_id").val());
                const newQty = parseFloat($("#edit_qty").val());
                const newPrice = parseFloat($("#edit_price").val());

                if (!newQty || !newPrice || newQty <= 0 || newPrice <= 0) {
                    toastr.error("Enter valid quantity and price");
                    return;
                }

                const row = $(`.product${id}`);
                const name = row.find("td").eq(1).text();
                const gst = parseFloat(row.find("td").eq(4).text());
                const total = (newPrice * newQty) + ((newPrice * newQty * gst) / 100);

                row.html(`
                    <td>${row.index() + 1}</td>
                    <td>${name}</td>
                    <td>${newQty}</td>
                    <td>${newPrice}</td>
                    <td>${gst}</td>
                    <td>${total.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-primary edit btn-sm" data-id="${id}" data-price="${newPrice}" data-qty="${newQty}">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger remove btn-sm" data-id="${id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `);

                product_list = product_list.map(p => p.product_id === id ? {
                    ...p,
                    qty: newQty,
                    price: newPrice
                } : p);
                calculate_total();
                $("#editProductModal").modal('hide');
            });

            function calculate_total() {
                const total = product_list.reduce((acc, item) => {
                    const base = item.qty * item.price;
                    const gstAmt = item.gst ? (base * item.gst / 100) : 0;
                    return acc + base + gstAmt;
                }, 0);
                $("#subtotal").text(total.toFixed(2));
            }

            $("#SavePO").on("click", function() {
                if (!$("#vendor_id").val()) return toastr.error("Select Vendor");
                if (!$("#name").val()) return toastr.error("Enter PO name");
                if (product_list.length === 0) return toastr.error("Add at least one product");


                $("#prod_list").val(JSON.stringify(product_list));
                $("#frmMain").submit();
            });
        });
        $(window).on("pageshow", function(event) {
            if (event.originalEvent.persisted) {
                // Browser back button used
                $("#frmMain")[0].reset();
                product_list = [];
                $("#prodList").html("");
                $("#subtotal").text("");
                $("#prod_list").val("");
            }
        });
    </script>
@endsection
