@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Return</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Return</h4>
            </div>
            <div>
                <form action="">
                    <select name="status" id="" class="form-control" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                    </select>
                </form>
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
                        <th> Customer</th>
                        <th> Invoice No.</th>
                        <th> Return Date</th>
                        <th> Description</th>
                        <th> Status</th>
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
                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->invoice_no }}</td>

                            <td>{{  myDateFormat($item->return_date) }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->user }}</td>
                            <td>

                                <a class="btn btn-sm btn-primary" href="/sale-return-challan-view/{{ $item->id }}"> <i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                                @if ($item->status == 'pending')
                                    <a class="btn btn-dark btn-sm"
                                        href="/sale-return-approve/{{ $item->id }}">Approve</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveSaleReturn') }}"
                id="frmMain">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add </span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">

                        <div class="col-md-4">
                            <label for="">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }}</option>
                                @endforeach
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
                            @php
                                $now_days = date('l');
                            @endphp
                            <label for="">Select Order Type</label>
                            <select name="order_type_id" id="order_type_id" class="form-control">

                                <option value="">Select</option>
                                @foreach ($order_type as $item)
                                    @php
                                        $disabled = '';
                                        $orderDaysArray = explode(', ', $item->week_days); // Convert string to array
                                        if (!in_array($now_days, $orderDaysArray)) {
                                            $disabled = 'disabled';
                                        }
                                    @endphp
                                    <option value="{{ $item->id }}" data-days="{{ $item->days }}"
                                        {{ $disabled }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="">Select Product</label>
                            <select name="product_id" id="product_id" class="form-control">

                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Qty</label>
                            <input type="number" name="qty" id="qty" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="">Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="current_stock">Current Stock</option>
                                <option value="scrap">Scrap</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="">Order Type</label>
                            <select class="form-control" name="status" id="status">
                                <option value="complete">Complete</option>
                                <option value="pending">Pending</option>
                            </select>
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
                                        <th>Type</th>
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
            $("#modal_name").text("Add Sale Return");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $("#order_type_id").on("change", function() {


            price = "";
            location_id = "";
            product_id = "";
            product_list = [];
            sno = 1;
            $("#prodList").html("")

            var order_type_id = $(this).val()
            var customer_id = $("#customer_id").val()


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
                            '"  data-gst="' + element.gst + '"   >' + element.name +
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


        var product_list = [];
        var sno = 1;
        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var qty = parseInt($("#qty").val())
            var type = ($("#type").val())


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
                            <td>${type}</td>    
                         
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
                type

            });

        });

        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#btnSave").on("click", function() {
            $('#prod_list').val(JSON.stringify(product_list));
            if (!$("#customer_id").val()) {
                toastr.error("Select Customer");
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
