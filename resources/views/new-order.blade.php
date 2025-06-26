@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>New Order </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>New Order</h4>
            </div>

        </div>
        <div class="card-body">
            <form id="frmMain" class="row" method="post" action="{{ route('SaveNewOrder') }}"
                enctype="multipart/form-data">
                @csrf
                <div class=" table-responsive">
                    <div class="row mt-3">

                        <div class="form-group col-md-3">
                            <label>Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                <option value="">--Select Customer--</option>
                                @foreach ($customers as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Store Location</label>
                            <select name="location_id" id="location_id" class="form-control" required>
                                <option value="">--Select Location--</option>
                                @foreach ($store as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->address }})</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label>Expected Packing Date</label>
                            <input type="date" name="packing_date" id="expected_packing_date" class="form-control"
                                required>

                        </div>


                        <div class="col-md-3">
                            <label>Expected Delivery Date</label>
                            <input type="date" name="delivery_date" id="expected_delivery_date" class="form-control"
                                required>

                        </div>

                        <br>

                        <div class="col-md-6">
                            <label>Description</label>
                            <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                        </div>
                    </div>
                    <div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Upload Requriemnt File<span class="text-danger"> *Only CSV File*</span></label>
                            <input type="file" id="file">

                        </div>

                        <div class="col-md-4">

                            <button class="btn btn-dark" type="button" id="BtnUpload">Upload</button>

                        </div>
                        <div class="col-md-4">
                            <a href="import-requirement-list.csv" class="btn btn-success btn-sm"
                                download="import-requirement-list.csv">Download sample file</a>

                        </div>

                    </div>
                    <br>
                    <p><strong> To add products manually select required article here..</strong></p>
                    <table class="table table-bordered mt-3">
                        <tr>

                            <td>
                                <select class="form-control" id="brand_id" name="brand_id">
                                    <option value="">Select Brand</option>
                                    @foreach ($brand as $item)
                                        <option value="{{ $item->id }}" data-brand_name="{{ $item->name }}">
                                            {{ $item->name }}</option>
                                    @endforeach




                                </select>

                            </td>

                            <td>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select Category</option>




                                </select>

                            </td>
                            <td>
                                <select id="sub_category_id" name="sub_category_id" class="form-control">
                                    <option>Select subcategory</option>
                                </select>

                            </td>
                            <td>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">--Select Product--</option>

                                </select>

                            </td>
                            <td>
                                <input type="number" name="price" id="price" placeholder="Enter Price"
                                    class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="qty" id="qty" placeholder="Enter Quantity"
                                    class="form-control" required>
                            </td>

                            <td>

                                <button type="button" onclick="addItem()"
                                    class="btn btn-sm btn-outline-success">ADD</button>
                            </td>
                        </tr>
                    </table>
                    <table class="table">
                        <thead style="color: black;">

                            <tr>

                                <th>Brand</th>
                                <th>Product</th>
                                <th>Article Code</th>
                                <th>Price</th>
                                <th>Qty</th>


                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productList">

                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="prod_list" id="prod_list" value="">
                <div class="d-flex justify-content-center mt-3">
                    <div class="col-md-3">
                        <label><strong> Transactional Password</strong></label>
                        <input type="password" name="password" id="password" placeholder="Enter Password"
                            class="form-control" required style="border: solid; color: black;">

                    </div>
                </div>

                <div class="text-center col-md-12 mt-3">

                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>

        </div>

    </div>
    <script>
        $("#brand_id").on("change", function() {
            $.ajax({
                url: "/GetCategory",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Category----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#category_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        })


        $("#category_id").on("change", function() {
            $.ajax({
                url: "/GetSubCategory",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Sub Category----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#sub_category_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        })

        $("#sub_category_id").on("change", function() {
            $.ajax({
                url: "/GetProducts",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Product----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '" data-article_code="' +
                            element.article_no + '" data-price="' +
                            element.price + '">' + element.name +
                            '</option>';
                    });
                    $("#product_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });

        $("#product_id").on("change", function() {
            $("#price").val($(this).find(":selected").data("price"))
        });


        var products = [];
        var row;

        function addItem() {
            var id = $('#product_id').val()
            var prod_name = $('#product_id option:selected').text()
            var qty = $('#qty').val()
            var product_code = $("#product_id").find(':selected').data('article_code')
            var price = $("#product_id").find(':selected').data('price')
            var brand = $("#brand_id").find(':selected').data('brand_name')


            if (qty <= 0) {
                alert('Qty should be more than zero.');
                return;
            }
            if (id <= 0) {
                alert('Select product.');
                return;
            }

            var ex_p = products.filter((item, index) => item.id == id)
            console.log(products[id])
            if (products[id] != undefined) {
                alert('This product already added.');
                return;
            }
            row = `
            <tr class="prod${id}">
                <td>${brand}</td>
                <td>${prod_name}</td>
                <td>${product_code}</td>
                <td>${price}</td>
                <td>${qty}</td>
                <td><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
            </tr>
        `;

            $('#productList').append(row);
            //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
            products.push({
                id,
                qty,
                price,

            });


            $('#product_id').val('')
            $('#qty').val('')
            $('#price').val('')
        }

        function removeItem(id) {
            $(`.prod${id}`).remove();
            products = products.filter(item => item.id !== id);
        }

        function saveBill() {
            $('#prod_list').val(JSON.stringify(products));
            if ($("#t_password").val() == false) {
                toastr.error("Enter Password");
                return;
            }
            $('#frmMain').submit()
        }


        $("#BtnUpload").on("click", function() {

            let fileInput = document.getElementById('file');
            let file = fileInput.files[0];
            if (file) {
                // Create a new FormData object
                let formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: "/UploadRequirementList",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        data = JSON.parse(result)
                        data.data.forEach(element => {
                            row += `
                            <tr class="prod${element.id}">
                                <td>${element.brand_name}</td>
                                <td>${element.name}</td>
                                <td>${element.article_no}</td>
                                <td>${element.price}</td>
                                <td>${element.qty}</td>
                                <td><button onclick="removeItem(${element.qty})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                        `;

                            let id = element.id;
                            let qty = element.qty;
                            let price = element.price;
                            products.push({
                                id,
                                qty,
                                price,

                            });
                        });

                        $('#productList').append(row);


                    },
                    error: function(data) {
                        console.log(data);

                    }
                });
            } else {
                toastr.error("Select CSV file for upload");
            }

        });
    </script>
@endsection
