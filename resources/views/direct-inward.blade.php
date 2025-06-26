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
            <form method="POST" class="needs-validation" id="UploadForm" novalidate action="{{ route('SaveDirectInward') }}">
                @csrf

                <div class="row">






                    <div class="col-md-3 mt-3">
                        <label for="">Product</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">Select product</option>
                            @foreach ($finish_products_mst as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2 mt-3">
                        <label for="">Qty</label>
                        <input type="number" class="form-control" id="qty" name="qty" required>

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

            $("select").select2();
        })
        var price = "";
        var location_id = "";
        var product_id = "";
        var product_list = [];
        var sno = 1;









        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var qty = parseInt($("#qty").val())
    

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
        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#UploadForm").on("submit", function() {

            $('#prod_list').val(JSON.stringify(product_list));
            $("#btnSubmit").attr("disabled", "disabled")

        })
    </script>
@endsection
