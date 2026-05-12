@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Invoices</title>
    @endpush
    <style>
        .wrap-text {
            word-wrap: break-word !important;
            overflow-wrap: break-word;
            white-space: normal !important;
            max-width: 200px;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Invoices</h4>
            </div>
            @php
                $status = request('status');
            @endphp

            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>

                    <div class="mx-2">
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>
                    <div class="">
                        <label for="">Customer Type</label>
                        <select name="order_type" id="order_type" onchange="this.form.submit()"
                            class="form-control select2 " required>
                            <option value="">Select</option>
                            <option value="customer" {{ request('order_type') == 'customer' ? 'selected' : '' }}>
                                Customer
                            </option>
                            <option value="outlet" {{ request('order_type') == 'outlet' ? 'selected' : '' }}>Outlet
                            </option>
                        </select>

                    </div>
                </form>

            </div>
            <div>

                <form action="{{ route('bulk-invoice-view') }}" method="POST">
                    @csrf
                    <input type="text" name="printBulkInvoiceID" id="printBulkInvoiceID" hidden>
                    <button class="btn btn-primary" id="btnPrintBulkInvoice"><i class="fa fa-print" aria-hidden="true"></i>
                        Print Bulk
                        Invoice</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th> <input type="checkbox" id="checkAll"> </th>
                        <th>Order Type </th>
                        <th>Customer </th>


                        <th>Invoice No </th>
                        <th>Invoice Date </th>
                        <th>Transport </th>
                        <th>Contact Person </th>
                        <th>User </th>
                        <th>Action </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td> <input type="checkbox" class="checkAll" data-id="{{ $item->id }}"
                                    data-type="{{ $item->ordType }}"> </td>
                            <td>{{ $item->ordType }}</td>
                            <td class="wrap-text" style="width:25%">{{ $item->customer }}</td>
                            <td>{{ $item->order_no }}</td>
                            <td>{{ myDateFormat($item->invoice_date) }}</td>
                            <td>
                                <p class="mb-1">{{ $item->transport }}</p>
                                <p class="mb-1">Vehicle : {{ $item->vehicle_no }}</p>
                            </td>
                            <td>
                                <p class="mb-1">Name : {{ $item->contact_person }}</p>
                                <p class="mb-1">Contact : {{ $item->number }}</p>
                            </td>

                            <td>{{ $item->user }}</td>
                            <td>

                                @if ($item->ordType == 'Advance Order')
                                    <a class="btn btn-primary btn-sm" href="/advance-invoice-view/{{ $item->id }}"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn btn-primary btn-sm" href="/invoice-view/{{ $item->id }}"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                @endif

                                @if ($item->status != 'cancel')
                                    @if ($item->ordType != 'Advance Order')
                                        <button style="background-color: orange" class="btn btn-sm btnCancelInvoice"
                                            value="{{ $item->id }}" type="button">Cancel</button>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Cancelled </span>
                                @endif




                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>


    <form action="{{ route('SaveCustomerOutwardStatus') }}" method="post">
        @csrf

        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delivered
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        You are going to delivered this challan.....
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

    <form action="{{ route('ConvertToInvoice') }}" method="post">
        @csrf

        <div class="modal fade" id="convertInvoice">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Convert to invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="cid" name="id">
                        You are going to convert this challan to invoice.....
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

    <form action="{{ route('cancelRegularInvoice') }}" method="POST" id="cancelInvoiceForm">
        @csrf

        <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Cancel Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                            <h4 class="text-danger fw-bold">Are you sure you want to cancel this invoice?</h4>
                            <input type="hidden" name="id" id="cancelID" hidden>
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
                            Verify & Cancel Invoice
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>


    <script>
        $(document).on("click", ".delivered", function() {
            $("#id").val($(this).val())
            $("#modalId").modal("show")
        })
        $(document).on("click", ".convertInvoice", function() {
            $("#cid").val($(this).val())
            $("#convertInvoice").modal("show")
        });

        $(document).ready(function() {
            $(document).on("click", ".btnCancelInvoice", function() {
                $("#cancelID").val($(this).val())
                $("#cancelInvoiceModal").modal("show")
            });

            let otp = null;
            $("#sendOtpBtn").on("click", function() {


                $.ajax({
                    url: '/sendCancelInvoiceOTP',
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
                        $("#verifyOtpBtn").text("Verify & Cancel Invoice");

                    },
                });

            })
        });

        $("#checkAll").on("click", function() {
            $(".checkAll").prop("checked", $(this).prop("checked"));
            getSelectedOrders();
        });

        $(document).on("change", ".checkAll", function() {
            getSelectedOrders();
        });
        $("#btnPrintBulkInvoice").hide();

        function getSelectedOrders() {

            let orders = [];

            $(".checkAll:checked").each(function() {

                orders.push({
                    id: $(this).data("id"),
                    order_type: $(this).data("type")
                });

            });

            $("#printBulkInvoiceID").val(JSON.stringify(orders));
            if (orders.length > 1) {
                $("#btnPrintBulkInvoice").show();
            } else {
                $("#btnPrintBulkInvoice").hide();
            }
        }
    </script>
@endsection
