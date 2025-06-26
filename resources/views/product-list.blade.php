@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Product List </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Product List</h4>
            </div>
            <div class="">
                <form method="GET" {{ route('product-list') }}>
                    <div class=" mt-3 d-flex justify-content-between">
                        <div>
                            <input type="hidden" name="status" value="{{ request('status') }}">

                            <button class="btn  {{ request('status') == 'pending' ? 'btn-success' : 'btn-primary' }}"
                                name="status" value="pending">Pending</button>
                            <button class="btn  {{ request('status') == 'processing' ? 'btn-success' : 'btn-primary' }}"
                                name="status" value="processing">Processing</button>
                            <button class="btn  {{ request('status') == 'complete' ? 'btn-success' : 'btn-primary' }}"
                                name="status" value="complete">Complete</button>


                        </div>


                    </div>


                </form>

            </div>

        </div>
        <div class="card-body">

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Location</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($products as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->user }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td>
                                @if ($item->status!="complete")
                                <button class="btn btn-sm btn-primary processing" value="{{ $item->id }}"
                                    data-qty="{{ $item->qty }}" data-make_qty="{{ $item->make_qty }}" type="button">
                                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                    
                                @endif
                            

                                <button class="btn btn-sm btn-info product_process" value="{{ $item->id }}"
                                    type="button">
                                    <i class="fa fa-history" aria-hidden="true"></i></button>

                                    <a class="btn btn-secondary btn-sm" href="/product-raw-material-view/{{$item->id}}"> <i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('ProcessProducts') }}" id="ProcessGenSetForm">
        @csrf

        <div class="modal fade" id="processingModal">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Process
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">


                                <input type="hidden" id="f_product_id" name="id">
                                <input type="hidden" id="actual_qty">
                                <input type="hidden" id="make_qty">
                                <label for="">Enter Process qty</label>
                                <input type="number" id="qty" name="qty" class="form-control" value="1"
                                    min="1">

                            </div>
                            <div class="mt-4">
                                <div id="alert">

                                </div>

                                <table id="stockTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Current Stock</th>


                                        </tr>
                                    </thead>
                                    <tbody id="tableRaw">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" id="CheckCSStock" class="btn btn-primary">Check Current Stock</button>
                        <button type="button" class="btn btn-info" id="btnProcess">Process</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <div class="modal fade" id="processingProductionModal">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Process
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productionProductList">

                        </tbody>

                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>


                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('CompleteProduction') }}" method="POST">
        @csrf
        <div class="modal fade" id="completeProductionModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Complete Production
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="id">
                        <h4>You are going to complete production.</h4>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">
                            Save
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
        $(document).on("click", ".processing", function() {
            $("#f_product_id").val($(this).val())
            $("#actual_qty").val($(this).data("qty"))
            $("#make_qty").val($(this).data("make_qty"))
            $("#CheckCurrentStock").hide()
            $("#btnProcess").hide()
            $("#GeneratePO").hide()
            $("#tableRaw").html("")
            $("#alert").html("")
            $("#processingModal").modal("show")
        })
        $("#btnProcess").hide()
        $("#CheckCSStock").on("click", function() {
            var actual_qty = parseInt($("#actual_qty").val());
            var id = $("#f_product_id").val();
            var qty = parseInt($("#qty").val());
            var make_qty = parseInt($("#make_qty").val());


            if (qty > actual_qty - make_qty) {
                $("#qty").val(1);
                $("#CheckCurrentStock").hide()
                $("#btnProcess").hide()
                $("#GeneratePO").hide()
                $("#tableRaw").html("")
                $("#alert").html("")
                toastr.error("Process qty can not be more then qty");
                return;
            }

            $.ajax({
                url: "/CheckCurrentStock",
                type: "POST",
                data: {
                    id: id,
                    qty: qty
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    result = JSON.parse(result)
                    $("#msg").hide()
                    var html = "";
                    var sno = 1;
                    result.data.forEach(element => {
                        var cl = "";
                        if (element.status == true) {
                            cl = "bg-soft-danger";
                        } else {
                            cl = "bg-soft-success";
                        }
                        html += `
                                    <tr >
                                        <td class="${cl}">${sno++}</td>
                                        <td class="${cl}">${element.product_name}</td>
                                        <td class="${cl}">${element.qty}</td>
                                        <td class="${cl}">${element.stock}</td>
                                       
                                            </button>
                                       
                                    </tr>
                                    `;

                    });
                    if (result.error == true) {
                        $("#GeneratePO").attr("href", "generate-po-product/" + id)
                        $("#alert").html(
                            "<div class='alert alert-danger'><strong>Raw material current stock insufficient</strong></div>"
                        )
                        $("#CheckCurrentStock").hide()
                        $("#GeneratePO").show()
                        $("#btnProcess").hide()
                    } else {
                        $("#alert").html(
                            "<div class='alert alert-success'><strong>Great you can process this order</strong></div>"
                        )
                        $("#CheckCurrentStock").hide()
                        $("#btnProcess").show()
                        $("#GeneratePO").hide()

                    }


                    $("#tableRaw").html(html)
                    $("#stockTable").show()
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });


        });

        $("#btnProcess").on("click", function() {
            $("#CheckCSStock").hide()
            var spinner = `<div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                                </div>`;
            $("#btnProcess").attr("disabled", "disabled")
            $("#btnProcess").html("Processing " + spinner)
            $("#ProcessGenSetForm").submit();

        });

        $(document).on("click", ".product_process", function() {
            var id = $(this).val()
            $.ajax({
                url: "/GetProductionProducts",
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

                    var html = "";
                    var sno = 1;

                    result.forEach(element => {
                        btn="";
                        if (element.status == "complete") {
                            btn = "<span class='badge bg-success'>Completed</span>";
                        } else {
                            btn =
                                `<button type="button" class="btn btn-primary btn-sm completeProduction" value="${element.id}" >Complete</button>`;
                        }
                        html += `
                                <tr>
                                    <td>${sno++}</td>
                                    <td>${element.qty}</td>
                                    <td>${element.status}</td>
                                    <td>${element.user}</td>
                                    <td>${element.created_at}</td>
                                    <td>${element.updated_at}</td>
                                    <td>
                                        
                                    ${btn}
                                    </td>
                                    </tr>
                            `;
                    });


                    $("#productionProductList").html(html)
                    $("#processingProductionModal").modal("show")
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });
        $(document).on("click", ".completeProduction", function() {
            $("#product_id").val($(this).val());
            $("#completeProductionModal").modal("show")
        })
    </script>
@endsection
