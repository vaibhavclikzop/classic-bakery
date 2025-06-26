@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>GenSet</h4>
            </div>
            <div class="">
                <form method="GET" {{ route('gen-set') }}>
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
                        <div>

                            <select name="team_id" id="team_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Select Team</option>
                                @foreach ($team as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('team_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>


                </form>

            </div>
        </div>
        <div class="card-body" id="">

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Created at</th>
                        <th>Customer Name</th>
                        <th>Team </th>
                        <th>Gen Set Name</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>User</th>


                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                        $customer = '';
                    @endphp
                    @foreach ($gen_set_mst as $item)
                        @if (!$item->customer_name)
                            @php
                                $customer_name = 'Direct';
                            @endphp
                        @else
                            @php
                                $customer_name = $item->customer_name;
                            @endphp
                        @endif
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ date('d-m-Y h:i A ', strtotime($item->created_at)) }}</td>
                            <td>{{ $customer_name }}</td>
                            <td>{{ $item->team }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->delivery_date)) }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->user_name }}</td>

                            <td>
                                @if ($item->status == 'pending')
                                    <button class="btn btn-danger btn-sm delete" data-id="{{ $item->id }}"
                                        type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                @endif


                                @if (request('status') != 'complete')
                                    <button class="btn btn-primary btn-sm ChangeStatus" data-id="{{ $item->id }}"
                                        data-status="{{ $item->status }}" data-location_id="{{ $item->location_id }}"
                                        type="button"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                @endif
                                <a class="btn btn-info btn-sm" href="/view-gen-set-details/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>


    <form method="POST" enctype="multipart/form-data" action="{{ route('ProcessGenSet') }}" id="ProcessGenSetForm">
        @csrf
        <div class="modal fade" id="changeStatusModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="location_id" name="location_id">
                        <h5 class="modal-title" id="modalTitleId">
                            GenSet
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 id="msg">First Check raw material in current stock.</h5>
                        <div>
                            <div id="alert">

                            </div>

                            <table id="stockTable" class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Current Stock</th>
                                        <th>Info</th>

                                    </tr>
                                </thead>
                                <tbody id="tableRaw">

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-info" id="CheckCurrentStock">Check Current Stock</button>
                        <button type="button" class="btn btn-info" id="btnProcess">Process</button>
                        <a href="" class="btn btn-info" id="GeneratePO">Generate PO</a>
                    </div>
                </div>
            </div>
        </div>
    </form>





    <form method="POST" enctype="multipart/form-data" action="{{ route('CompleteGenSet') }}" id="">
        @csrf
        <div class="modal fade" id="processingModal">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="hidden" id="cid" name="id">
                        <h5 class="modal-title" id="modalTitleId">
                            GenSet
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>you are going to complete this GenSet.</h5>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-info" id="">Complete</button>

                    </div>
                </div>
            </div>
        </div>
    </form>




    <div class="modal fade" id="serialNoModal">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h5 class="modal-title" id="">
                        Product Serial Number
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th> S.No</th>

                                <th>Product</th>

                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody id="SerialNumberList">

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



    <form method="POST" enctype="multipart/form-data" action="{{ route('DeleteGenSet') }}" id="">
        @csrf
        <div class="modal fade" id="deleteModal">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <input type="hidden" id="did" name="id">
                        <h5 class="modal-title text-white" id="modalTitleId">
                            Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>you are going to delete this GenSet.</h5>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-danger" id="">Delete</button>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .bg-soft-danger {
            background-color: #ffeded !important;
            color: #FF0000 !important;
        }

        .bg-soft-success {
            background-color: #d8ffdc !important;
            color: #28C76F !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#btnProcess").hide()
            $("#GeneratePO").hide();
            $(document).on("click", ".ChangeStatus", function() {
                var id = $(this).data("id")
                var status = $(this).data("status")
                var location_id = $(this).data("location_id")
                $("#location_id").val(location_id)
                $("#stockTable").hide()
                $("#alert").html("")
                $("#msg").show()
                $("#CheckCurrentStock").show()
                $("#btnProcess").hide()
                $("#GeneratePO").hide();
                $("#CheckCurrentStock").text("Check Current Stock")



                if (status == "pending") {
                    $("#id").val(id)
                    $("#changeStatusModal").modal("show")
                } else if (status == "processing") {
                    $("#cid").val(id)
                    $("#processingModal").modal("show")
                }

            });
            $("#stockTable").hide()
            $("#msg").show()


            $("#CheckCurrentStock").on("click", function() {

                var id = $("#id").val();


                $.ajax({
                    url: "/CheckCurrentStock",
                    type: "POST",
                    data: {
                        id: id
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
                                        <td class="${cl}">
                                            <button type="button" class="btn btn-info btn-sm ViewSno" data-product_id="${element.product_id}" >
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </td>
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
                var spinner = `<div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                                </div>`;
                $("#btnProcess").attr("disabled", "disabled")
                $("#btnProcess").html("Processing " + spinner)
                $("#ProcessGenSetForm").submit();

            })


            $(document).on("click", ".ViewSno", function() {


                var product_id = $(this).data("product_id");
                var location_id = $("#location_id").val();
                $.ajax({
                    url: "/GetSerialNumber",
                    type: "POST",
                    data: {
                        product_id: product_id,
                        location_id: location_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        var s_no = 1;
                        var SerialNumberList = "";
                        result.forEach(element => {
                            SerialNumberList += `
                                <tr>
                                    <td>${s_no++}</td>
                                    <td>${element.product_name}</td>
                                  
                                    <td>${element.sno}</td>
                                </tr>
                                `;
                        });
                        $("#SerialNumberList").html(SerialNumberList);


                        $("#serialNoModal").modal("show");
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });

            $(document).on("click", ".delete", function() {
                $("#did").val($(this).data("id"))
                $("#deleteModal").modal("show")
            })

        });
    </script>
@endsection
