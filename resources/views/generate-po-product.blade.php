@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Generate PO </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Generate PO Via Products</h4>
            </div>

        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" id="frmMain" novalidate action="{{ route('SavePoProducts') }}">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <label for="">GenSet Order</label>
                        <select name="genset_id" id="genset_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($gen_set_mst as $item)
                                <option value="{{ $item->id }}"> {{ $item->product }}, Delivery :
                                    {{ $item->delivery_date }} </option>
                            @endforeach
                        </select>

                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        <label for="">Products</label>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $item)
                                                <option value="{{ $item->id }}"
                                                    data-article_no="{{ $item->article_no }}"
                                                    data-gst="{{ $item->gst }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Qty</label>
                                        <input type="number" name="qty" id="qty" min="1" value="1"
                                            class="form-control" placeholder="Enter Qty">
                                    </th>
                                    <th>
                                        <label for="">Price</label>
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
                                    <th>Article No.</th>
                                    <th>Current Stock</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>GST</th>
                                    <th>GST Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">
                    </div>

                    <div class="text-center col-md-12 mt-3">

                        <button type="button" id="SavePO" name="btnSubmit" class="btn btn-warning">Submit</button>

                    </div>
                </div>
            </form>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            var product_list = [];
            var sno = 1;
            $("#genset_id").on("change", function() {
                var id = $(this).val()
                $.ajax({
                    url: "/GetGenSetProduct",
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
                        var list = ""
                        result.forEach(element => {
                            var product_id = element.product_id;
                            var qty = element.qty;
                            var price = element.price;
                            var product_name = element.product_name;
                            var article_no = element.article_no;
                            var gst = element.gst;
                            var gst_type="Inner GST";

                            list += `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td>${product_name}</td>    
                            <td>${article_no}</td>    
                            <td>${element.stock}</td>    
                          <td><input type="number" class="form-control qty" data-product_id="${product_id}" 
                                            value="${qty}"></td>    
                                               <td><input type="number" step="0.01" class="form-control price" data-product_id="${product_id}" 
                                            value="${price}"></td>    
                                            <td>${gst}</td>
                                            <td>
                                          
                                   
                                       <select name="gst_type"  class="form-control gst_type">
                                        <option value="Inner GST" data-product_id="${product_id}" >Inner GST</option>
                                        <option value="Outer GST" data-product_id="${product_id}" >Outer GST</option>
                                       </select>

                                   
                                            </td>
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;
                       
                            product_list.push({
                                product_id,
                                qty,
                                price,
                                gst,
                                gst_type
                            });

                        });
                        console.log(product_list)
                        $("#prodList").html(list)


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
                var qty = parseInt($("#qty").val())
                var price = parseFloat($("#price").val())
                var article_no = $("#product_id").find(":selected").data("article_no")
                var gst = $("#product_id").find(":selected").data("gst")
var gst_type="Inner GST";
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
                            <td>${article_no}</td>    
                            <td>Can find</td>    
                           <td><input type="number" class="form-control qty" data-product_id="${product_id}" 
                                            value="${qty}"></td>    
                                               <td><input type="number" step="0.01" class="form-control price" data-product_id="${product_id}" 
                                            value="${price}"></td> 
                                            <td>${gst}</td>  
                                            <td>
                                            
                                              <select name="gst_type"  class="form-control gst_type">
                                        <option value="Inner GST" data-product_id="${product_id}" >Inner GST</option>
                                        <option value="Outer GST" data-product_id="${product_id}" >Outer GST</option>
                                       </select>
                                                </td>
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
                    price,
                    gst,
                    gst_type
                });

            });
            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.product_id !== id);

            });



            $(document).on("keyup", '.qty', function() {
                var product_id = parseInt($(this).data("product_id"))

                var qty = parseInt($(this).val());

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {
                    product.qty = qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
            });

            $(document).on("change", '.gst_type', function() {
                var product_id = $(this).find(":selected").data("product_id")

                var gst_type = ($(this).val());

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {
                    product.gst_type = gst_type;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
            });


            $(document).on("keyup", '.price', function() {
                var product_id = parseInt($(this).data("product_id"))

                var price = parseInt($(this).val());

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {
                    product.price = price;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
            });

            $("#SavePO").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));


                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }

                $("#SavePO").attr("disabled", "disabled")
                $('#frmMain').submit()

            });


            @if (!empty($id))
                var id = {{ $id }}

                $("#genset_id").val(id).trigger("change");
            @endif
        });
    </script>
@endsection
