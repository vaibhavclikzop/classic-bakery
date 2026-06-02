@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>RM issue </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>RM issue</h4>
            </div>

        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" id="UploadForm" novalidate action="{{ route('SaveOutward') }}">
                @csrf

                <div class="row">
                    <div class="col-md-3 mt-3">
                        <label for="">Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach ($department as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-md-3 mt-3">
                        <label for="">Issue Date</label>
                        <input type="date" id="invoice_date" name="invoice_date" class="form-control"
                            value="{{ date('Y-m-d') }}" required>


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
                                    <th colspan="">
                                        <label for="">Product Type</label> <br>
                                        <select name="type" id="type" class="form-control ">
                                            <option value="">Select Type</option>
                                            <option value="raw_material">Raw Material</option>
                                            <option value="trading">Trading</option>
                                        </select>
                                    </th>
                                    <th colspan="4">
                                        <label for="">Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control ">
                                            <option value="">Select Product</option>

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Stock</label>
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
                                    <th>Type </th>
                                    <th colspan="3">Product </th>
                                    <th>Current Stock </th>
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
                        <button class="btn btn-success" id="btnSubmit" type="submit">Save Order</button>

                    </div>
                </div>
            </form>
        </div>

    </div>


    <script>
        var price = "";
        var location_id = "";
        var product_id = "";
        var product_list = [];
        var sno = 1;


        $("#product_id").on("change", function() {

            var stock = $(this).find(":selected").data("stock");
            $("#stock").val(stock)
            if (parseInt(stock) < 0) {
                toastr.error("This product current stock is zero");
                $("#product_id").val("");
                return;
            }


        });
        $(document).ready(function() {
            $("#product_id").select2();
        })



        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var qty = parseFloat($("#qty").val())
            var type = ($("#type").val())
            var stock = parseFloat($("#product_id").find(":selected").data("stock"))


            if (!product_id || isNaN(product_id)) {
                toastr.error("Select a valid Product");
                return;
            }

            if (!qty || isNaN(qty) || qty <= 0) {
                toastr.error("Enter a valid quantity");
                return;
            }

            if (stock < qty) {
                toastr.error("Qty can not be more then stock");
                return;
            }


            let existingProduct = product_list.find(product => product.product_id === product_id && product.type ===
                type);
            if (existingProduct) {
                toastr.error("Product already exists");
                return;
            }

            var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td  >${type}</td>    
                            <td colspan="3">${product_name}</td>    
                            <td>${stock}</td>    
                            <td><input type="number" step="0.01" value="${qty}" class="updateQty form-control" data-id="${product_id}" data-stock="${stock}"></td>    
                           
                
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
                type,

            });
            $("#product_id").val(null).trigger("change");
            $("#qty").val("")

        });



        $("#qty").on("keyup", function() {

            var qty = parseInt($(this).val())
            var stock = parseInt($("#product_id").find(":selected").data("stock"))
            if (stock < qty) {
                $("#qty").val(stock)
                toastr.error("Qty can not be more then stock");
                return;
            }
        });

        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#UploadForm").on("submit", function(e) {
            // Prevent default submission temporarily
            e.preventDefault();

            // 1. Check HTML5 form validation
            if (!this.checkValidity()) {
                this.classList.add('was-validated'); // Optional Bootstrap style
                return false;
            }

            // 2. Check product list is not empty
            if (product_list.length === 0) {
                return toastr.error("Add at least one product.");
            }

            // 3. All checks passed — inject JSON and disable button
            $('#prod_list').val(JSON.stringify(product_list));
            $("#btnSubmit").attr("disabled", true);

            // 4. Submit form manually
            this.submit();
        });

        $(document).on("keyup", ".updateQty", function() {

            let product_id = $(this).data("id");
            let qty = parseFloat($(this).val()).toFixed(2);

            let stock = parseFloat($(this).data("stock")).toFixed(2);

            if (qty > stock) {
                toastr.error("Qty can not be more then stock");
                $(this).val(stock)
                product_list = product_list.map(item => {
                    if (item.product_id == product_id) {
                        item.qty = stock;
                    }
                    return item;
                });
                console.log(product_list);
                return;
            }
            // update in array
            product_list = product_list.map(item => {
                if (item.product_id == product_id) {
                    item.qty = qty;
                }
                return item;
            });

            console.log(product_list);
        });

        $(document).ready(function() {


            $('#product_id, #qty').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#addProduct').click();
                }
            });

            $("#type").on("change", function() {
                $.ajax({
                    url: "/getRMorTradingItems",
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

                            html += '<option value="' + element.id + '" data-stock="' +
                                element.stock + '">' + element
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
        });
    </script>
@endsection
