@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Purchase Return</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Return</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Vendor</th>
                        <th> Invoice </th>
                        <th> Return Date</th>
                        <th> Description</th>
                        <th> User</th>


                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->company }} / {{ $item->vendor }}</td>
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->return_date }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->user }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="/purchase-return-challan-view/{{ $item->id }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i> </a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SavePurchaseReturn') }}"
                id="frmMain">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Category</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">

                        <div class="col-md-4">
                            <label for="">Select Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-control">
                                <option value="">Select Vendor</option>
                                @foreach ($vendor as $item)
                                    <option value="{{ $item->id }}">{{ $item->company }} / {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Select Invoice</label>
                            <select name="inward_id" id="inward_id" class="form-control">
                                <option value="">Select Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Date</label>
                            <input type="date" name="return_date" id="return_date" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <label>Product</label> <br>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Qty</label>
                            <input type="number" name="qty" id="qty" class="form-control">
                        </div>
                        <div class="col-md-2">

                            <button class="btn btn-primary mt-4" id="addProduct" type="button">Add</button>
                        </div>
                        <div class="col-12">
                            <input type="hidden" id="prod_list" name="prod_list">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="prodList">

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnSave">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#brand_id").val($(this).data("brand_id"));
            $("#modal_name").text("Update Category");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Purchase Return");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $("#vendor_id").on("change", function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "/GetInwardChallan",
                type: "POST",
                data: {
                    id: vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "<option>Select</option>";
                    result.forEach(element => {
                        html +=
                            `<option value='${element.id}'>  ${element.invoice_no}</option>`;
                    });
                    $("#inward_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        })

        $("#inward_id").on("change", function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "/GetInwardChallanProducts",
                type: "POST",
                data: {
                    id: vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "<option>Select</option>";
                    result.forEach(element => {
                        html +=
                            `<option value='${element.id}' data-qty="${element.qty}"> ${element.product} : Qty - ${element.qty}</option>`;
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


            if ($("#product_id").find(":selected").data("qty") < qty) {
                toastr.error("Qty can not be more then inward qty ");
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
                qty

            });

        });

        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#btnSave").on("click", function() {
            $('#prod_list').val(JSON.stringify(product_list));
            if (!$("#vendor_id").val()) {
                toastr.error("Select Vendor");
                return;
            }

            if (!$("#inward_id").val()) {
                toastr.error("Select Inward invoice");
                return;
            }

            if (product_list.length === 0) {
                toastr.error("Select at least one product");
                return;
            }


            $('#frmMain').submit()

        })
    </script>
@endsection
