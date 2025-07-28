@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Create Recipe </h4>


            </div>
            <div class="">

                {{-- <a href="generate-po-product" class="btn btn-dark">Generate PO Via Products</a> --}}

            </div>

        </div>
        <div class="card-body">
            <div class="alert alert-danger" role="alert" style="display: none">
                <strong id="vendor_name"> </strong>
            </div>

            <form method="POST" id="frmMain" action="{{ route('SaveRecipe') }}">
                @csrf

                <div class="row">

                    <div class="col-md-3">
                        <label for="">Recipe Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter Recipe Name">

                    </div>
                    <div class="col-md-3">
                        <label for="">Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Enter  Description">

                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">

                        <table class="table">
                            <thead>
                                <tr>
                                    <td colspan="3">
                                        Products <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $item)
                                                <option value="{{ $item->id }}" data-uom="{{ $item->unitType->name }}">
                                                    {{ $item->name }} </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        Qty <br>
                                        <div class="input-group has-validation">
                                            <input type="number" step="0.01" id="qty" class="form-control"
                                                required>
                                            <span class="input-group-text btn btn-dark" id="inputGroupPrepend"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary mt-3" type="button" id="addProduct">Add</button>
                                    </td>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product</th>
                                    <th> Qty</th>
                                    <th> UOM</th>
                                    <th> Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>

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



    <script>
        $(document).ready(function() {
            var product_list = [];
            var sno = 1;

            $("#vendor_id").select2();
            $("#product_id").select2();


            $("#product_id").on("change", function() {
                var uom = $(this).find(":selected").data("uom");
                $("#inputGroupPrepend").text(uom)
            });


            $("#addProduct").on("click", function() {
                var product_id = parseInt($("#product_id").val())
                var product_name = $("#product_id").find(":selected").text()

                var uom = $("#inputGroupPrepend").text();
                var qty = parseFloat($("#qty").val())




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
                            <td>${product_name}</td>    
                            <td>${qty}</td>    
                            <td>${uom}</td>    
                       
                                       
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
                    uom,
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
        });
    </script>
@endsection
