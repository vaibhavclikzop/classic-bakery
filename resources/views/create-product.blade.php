@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Create Product</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Create Product</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('SaveFProducts') }}" id="frmMain" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Select Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <option value="">Select Location</option>
                            @foreach ($location as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="">Select Product</label>
                        <select name="f_product_id" id="f_product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach ($finish_product as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Qty</label>
                        <input type="number" id="qty" name="qty" value="1" min="1"
                            class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="">Price</label>
                        <input type="number" step="0.01" id="price" value="0" name="price"
                            class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                    </div>


                </div>
                <hr>
                <div class="">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Location</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productList">

                        </tbody>

                    </table>

                    <div class="mt-4 text-center">
                        <input type="hidden" id="prod_list" name="prod_list">
                        <button class="btn btn-primary" type="submit" id="Save"> Submit </button>

                    </div>

                </div>
            </form>

        </div>

    </div>

    <script>
        $("#f_product_id").on("change", function() {
            $("#price").val($(this).find(":selected").data("price"))
        });


        var product_list = [];
        var sno = 1;
        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#f_product_id").val())
            var product_name = $("#f_product_id").find(":selected").text()
            var qty = parseInt($("#qty").val())
            var price = parseFloat($("#price").val())
            var location_id = parseFloat($("#location_id").val())
            var location = $("#location_id").find(":selected").text()
            if (!location_id || isNaN(location_id)) {
                toastr.error("Select a valid location");
                return;
            }

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
                            <td>${location}</td>    
                            <td>${product_name}</td>    
                            <td>${qty}</td>    
                            <td>${price}</td>    
                             
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

            $("#productList").append(html)
            product_list.push({
                product_id,
                qty,
                price,
                location_id,

            });

        });
        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#Save").on("click", function() {





            if (product_list.length === 0) {
                toastr.error("Select at least one product");
                return;
            }
            $('#prod_list').val(JSON.stringify(product_list));



            $('#frmMain').submit()

        })
    </script>
@endsection
