@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Orders</title>
    @endpush
    <div class="card">

        <div class="card-header ">
            <div class="d-flex justify-content-between">


                <div class="page-title">
                    <h4>Orders</h4>
                </div>



                <div class="">

                    @if ($status == 'processing')
                        {{-- <a href="/order-summary-department-wise?id={{ $department->id }}" class="btn btn-primary mx-1">Order
                        Summary Department Wise</a> --}}
                        <a href="/department-wise-treading?id={{ $department->id }}" class="btn btn-info mx-1">Department
                            wise Treading Report
                        </a>
                        <a href="/order-summary-customer-wise?id={{ $department->id }}" class="btn btn-info mx-1">Department
                            wise order report
                        </a>
                        <a href="/order-summary-shop-wise?id={{ $department->id }}" class="btn btn-info mx-1">Shop wise
                            order
                            report
                        </a>
                        <button class="btn btn-dark" type="button" data-bs-toggle="modal"
                            data-bs-target="#CompleteProduction">Production Complete </button>
                    @endif

                </div>
            </div>
            <div>
                <form action="" class="d-flex">

                    <div class="d-flex mt-3 col-3 fliat">
                        <a class="btn btn-info" href="?date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                            << </a>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ request('date') ?? date('Y-m-d', strtotime('+1 day')) }}"
                                    onchange="this.form.submit()">
                                <a class="btn btn-info"
                                    href="?date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                    >>
                                </a>

                    </div>
                    <div>
                        <select name="type" onchange="this.form.submit()" class="form-control mx-3 mt-3">

                            <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @if ($status == 'pending')
            <form method="POST" action="{{ route('GenerateWorkOrder') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                @csrf
                <button class="btn btn-dark float-end">Proceed Order</button>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checks" checked> </th>
                            <th>S.no</th>
                            <th> Order ID</th>
                            <th> Customer Name</th>
                            <th> Order Type</th>

                            <th>Order Date</th>
                            <th>Delivery Date</th>
                            <th>Status</th>


                            <th>User</th>

                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($orders as $item)
                            <tr>
                                <th>
                                    <input type="checkbox" checked name="order_ids[]" value="{{ $item->id }}"
                                        class="checks">
                                </th>
                                <th>{{ $sno++ }}</th>
                                <th>{{ $item->order_id }}</th>
                                <th>{{ $item->customer }}</th>
                                <th>{{ $item->category }}</th>

                                <th>{{  myDateFormat($item->order_date) }}</th>
                                <th>{{  myDateFormat($item->delivery_date) }}</th>
                                <th>

                                    @if ($item->status == 'complete')
                                        <span class="badge bg-success">Complete</span>
                                    @elseif ($item->order_status == 'fresh')
                                        <span class="badge bg-dark">Fresh</span>
                                    @else
                                        <span class="badge bg-warning">Partial</span>
                                    @endif
                                </th>


                                <th>{{ $item->user }}</th>

                                <th>



                                    @if ($item->status == 'dispatch' || $item->status == 'pending')
                                        <a href="/outward-customer-order?id={{ $item->id }}&customer_id={{ $item->customer_id }}"
                                            class="btn btn-secondary btn-sm">Outward</a>
                                    @endif


                                    <a href="/order-view/{{ $item->id }}" class="btn btn-secondary btn-sm"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                    @if ($item->status == 'pending')
                                        <button class="btn btn-danger btn-sm cancel_status" value="{{ $item->id }}"
                                            data-status="{{ $item->status }}" type="button"><i class="fa fa-xmark"
                                                aria-hidden="true"></i></button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </form>
        @else
            <div class="card-body">
                <form action="{{ route('CompleteProduction') }}" method="POST" id="formMain">

                    @csrf
                    <table class="table dataTable table-hover">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th><input type="checkbox" id="checks"></th>
                                <th> Order ID</th>
                                <th> Order Type</th>
                                <th> Customer Name</th>


                                <th>Delivery Date</th>
                                <th>Status</th>

                                <th>User</th>

                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($orders as $item)
                                <tr>
                                    <th>{{ $sno++ }}</th>
                                    <th><input type="checkbox" name="order_ids[]" value="{{ $item->id }}"
                                            class="checks">
                                    </th>
                                    <th>{{ $item->order_id }}</th>
                                    <th style="white-space: normal;">{{ $item->category }}</th>

                                    <th>{{ $item->customer }}</th>


                                    <th>{{  myDateFormat($item->delivery_date) }}</th>
                                    <th>

                                        @if ($item->status == 'complete')
                                            <span class="badge bg-success">Complete</span>
                                        @elseif ($item->order_status == 'fresh')
                                            <span class="badge bg-dark">Fresh</span>
                                        @else
                                            <span class="badge bg-warning">Partial</span>
                                        @endif
                                    </th>


                                    <th>{{ $item->user }}</th>

                                    <th>



                                        {{-- @if ($item->status != 'complete')
                                     <a class="btn btn-sm btn-info" href="/outward-order">Outward</a>
                                @endif --}}


                                        <a href="/order-view/{{ $item->id }}" class="btn btn-secondary btn-sm"><i
                                                class="fa fa-eye" aria-hidden="true"></i></a>
                                        @if ($item->status == 'dispatch' || $item->status == 'pending')
                                            <a href="/outward-customer-order?id={{ $item->id }}&customer_id={{ $item->customer_id }}"
                                                class="btn btn-secondary btn-sm">Outward</a>
                                        @endif
                                        @if ($item->status == 'processing')
                                            <a class="btn btn-primary btn-sm" href="/create-order?id={{$item->id}}"  
                                                value="{{ $item->id }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            <button class="btn btn-danger btn-sm btnCancelInvoice" type="button"
                                                value="{{ $item->id }}">Cancel</button>
                                        @endif

                                    </th>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </form>
            </div>
        @endif



    </div>

    <form action="{{ route('SaveOrderStatus') }}" class="needs-validation" novalidate method="POST">
        @csrf
        <div class="modal fade" id="statusModal">
            <div class="modal-content modal-dialog">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Order Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <label for="">Select Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="processing">Processing</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="delivered">Delivered</option>
                    </select>
                    <div class="mt-3 dispatch_div">
                        <label for="">Dispatch Date</label>
                        <input type="date" name="dispatch_date" id="dispatch_date" class="form-control">

                    </div>
                    <div class="mt-3 delivered_div">
                        <label for="">Delivered Date</label>
                        <input type="date" name="delivered_date" id="delivered_date" class="form-control">

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
        </div>
    </form>


    <form action="{{ route('ShiftOrder') }}" class="needs-validation" novalidate method="POST">
        @csrf
        <div class="modal fade" id="shiftCustomerModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Shift Customer
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="sid" name="id">
                        <label for="">Customers</label>
                        <select name="customer_id" id="" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <form action="" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="CompleteProduction" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Complete Production</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>You are going to complete production</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnCompleteProduction">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('CancelOrder') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="cancelOrderModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content p-5 px-3 text-center">
                            <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i
                                    class="fa fa-trash fs-24 text-danger"></i></span>
                            <h4 class="fs-20 text-gray-9 fw-bold mb-2 mt-1">Cancel Order</h4>
                            <input type="hidden" id="deleteId" name="id">
                            <p class="text-gray-6 mb-0 fs-16">Enter password to cancel order?</p>
                            <div class="pass-group" style="position: relative;max-width: 300px; margin: 0 auto;">
                                <input type="password" class="pass-input form-control" value="" name="order_pwd"
                                    required>

                            </div>


                            <div class="modal-footer-btn mt-3 d-flex justify-content-center">
                                <button type="button" class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Yes
                                    Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <form action="{{ route('CancelOrder') }}" method="POST" id="cancelInvoiceForm">
        @csrf

        <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Cancel Processing Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                            <h4 class="text-danger fw-bold">Are you sure you want to cancel this order ?</h4>


                            <input type="hidden" name="id" id="cancelID" hidden>
                        </div>
                        <div>

                            <p class="text-danger fw-bold mt-3">
                                ⚠️ Attention: Please read the following instructions carefully before proceeding. This
                                action will directly impact your production stock records.
                            </p>
                            <ol class="text-danger">
                                <li>This action is irreversible.</li>
                                <li>Stock will be deducted for all items in this production order.</li>

                            </ol>
                        </div>
                        <div class="mb-3 mt-3" id="btnSection">
                            <button type="button" class="btn btn-dark w-100" id="sendOtpBtn">
                                Send OTP
                            </button>
                        </div>

                        <!-- OTP Input -->
                        <div class=" mt-3 d-none" id="otpSection">
                            <label>Enter OTP</label>
                            <input type="text" id="otp" class="form-control" placeholder="Enter OTP">
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger d-none w-100" id="verifyOtpBtn" type="button">
                            Verify & Cancel Order
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>


    <script>
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        $(".dispatch_div").hide()
        $(".delivered_div").hide()
        $(document).on("click", ".btnEdit", function() {
            $("#id").val($(this).data("id"))
            $("#status").val($(this).data("status"))
            var delivered_date = $(this).data("delivered_date")
            var dispatch_date = $(this).data("dispatch_date")

            var status = $(this).data("status");

            $("#dispatch_date").val(dispatch_date)
            $("#delivered_date").val(delivered_date)
            if (status == "dispatched") {
                $(".dispatch_div").show(500)
                $(".delivered_div").hide(500)

            } else if (status == "delivered") {
                $(".dispatch_div").hide(500)
                $(".delivered_div").show(500)
            } else {
                $(".dispatch_div").hide(500)
                $(".delivered_div").hide(500)

            }
            $("#statusModal").modal("show");
        });
        $("#status").on("change", function() {
            var status = $(this).val();
            if (status == "dispatched") {
                $(".dispatch_div").show(500)
                $(".delivered_div").hide(500)

            } else if (status == "delivered") {
                $(".dispatch_div").hide(500)
                $(".delivered_div").show(500)
            } else {
                $(".dispatch_div").hide(500)
                $(".delivered_div").hide(500)

            }
        });
        $(document).on("click", ".shiftOrder", function() {
            var id = $(this).data("id")
            $("#sid").val(id)
            $("#shiftCustomerModal").modal("show");
        });

        $(document).on("click", ".cancel_status", function() {
            $("#deleteId").val($(this).val())
            $("#cancelOrderModal").modal("show")
        });
        $("#checks").on("click", function() {
            if ($(this).prop("checked") === true) {
                $(".checks").prop("checked", true);
            } else {
                $(".checks").prop("checked", false);
            }
        });
        $("#btnCompleteProduction").on("click", function() {
            if ($(".checks:checked").length === 0) {
                alert("Select at least one order");
                return;
            }
            $("#formMain").submit();
        });



        $(document).ready(function() {
            $(document).on("click", ".btnCancelInvoice", function() {
                $("#cancelID").val($(this).val())
                $("#cancelInvoiceModal").modal("show")
            });

            let otp = null;
            $("#sendOtpBtn").on("click", function() {


                $.ajax({
                    url: '/cancelOrderOTP',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#sendOtpBtn").attr("disabled", "disabled");
                        $("#sendOtpBtn").text("Sending OTP....");

                    },
                    success: function(res) {
                        let data = res;
                        if (data.status == true) {


                            let data = res;
                            otp = data.OTP;
                            $('#otpSection').removeClass('d-none');
                            $('#verifyOtpBtn').removeClass('d-none');
                            $("#btnSection").addClass("d-none")
                            toastr.success(data.message)
                        } else {
                            toastr.error(data.message)
                        }
                    }
                });
            });
            $("#verifyOtpBtn").on("click", function() {
                let clientOTP = $("#otp").val().trim();


                $.ajax({
                    url: '/verifyCancelOTP',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        otp: clientOTP,
                    },
                    beforeSend: function() {
                        $("#verifyOtpBtn").attr("disabled", "disabled");
                        $("#verifyOtpBtn").text("Verifying OTP....");

                    },
                    success: function(res) {
                        if (res.status == false) {

                            toastr.error(res.message)
                            return;
                        } else {
                            $("#verifyOtpBtn").attr("disabled", "disabled");
                            $("#verifyOtpBtn").text("Verifying OTP....");
                            $("#cancelInvoiceForm").submit();
                        }

                    },
                    complete: function() {
                        $("#verifyOtpBtn").removeAttr("disabled");
                        $("#verifyOtpBtn").text("Verify & Cancel Order");

                    },
                });

            })
        });
    </script>
@endsection
