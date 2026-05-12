@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4> Advance Order</h4>
        </div>
        <div class="">
            <form class="d-flex" method="GET">

                <a class="btn btn-info" href="?date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                    << </a>
                        <input type="date" name="date" class="form-control" required
                            value="{{ request('date') ?? date('Y-m-d', strtotime('+1 day')) }}"
                            onchange="this.form.submit()">
                        <a class="btn btn-info"
                            href="?date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                            >>
                        </a>
            </form>
        </div>
    </div>
    <div class="card-body">
        <form action="">
            <div>
                <button type="submit" value="printOrder" name="printOrder" class="btn btn-primary mx-2">Print Bulk
                    Order</button>
            </div>
            <div>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th><input type="checkbox" id="all_check"></th>
                            <th>Order ID</th>
                            <th>Outlet</th>
                            <th>Order Date</th>
                            <th>Delivery Date Time</th>
                            <th>Order Type</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    @php
                    $sno = 1;
                    @endphp
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="all_check">
                            </td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ myDateFormat($item->order_date) }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->delivery_date . ' ' . $item->delivery_time)->format('d-m-Y g:i A') }}
                            </td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                @if ($item->is_invoice == 1)
                                <a class="btn btn-sm btn-primary"
                                    href="/advance-invoice-view/{{ $item->id }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i> </a>
                                @else
                                <a class="btn btn-sm btn-primary"
                                    href="/advance-order-view/{{ $item->id }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i> </a>
                                @endif

                                @if ($item->status != 'cancel')
                                @if ($item->status != 'delivered')
                                <button class="btn btn-dark btn-sm change_status"
                                    value="{{ $item->id }}" data-status="{{ $item->status }}"
                                    type="button"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                @endif
                                @endif
                                @if ($item->status == 'pending')
                                <button class="btn btn-danger btn-sm cancel_status" value="{{ $item->id }}"
                                    data-status="{{ $item->status }}" type="button"><i class="fa fa-xmark"
                                        aria-hidden="true"></i></button>
                                @endif

                                @if ($item->is_invoice == 0)
                                <button class="btn btn-dark btn-sm convertToInvoice"
                                    value="{{ $item->id }}" type="button">Convert to invoice</button>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>


<form action="{{ route('UpdateStatus') }}" method="POST" class="needs-validation" novalidate>
    @csrf

    <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Change Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <select name="status" id="status" class="form-control" required>
                        <option value="">Select</option>
                        <option value="dispatch">Dispatch</option>
                        <option value="complete">Out for Delivery</option>
                        <option value="delivered">Delivered</option>

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

<!-- new cancel form -->
<form action="{{ route('Cancel_order') }}" method="POST" id="cancelOrderForm">
    @csrf

    <div class="modal fade" id="cancelOrderModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div style="text-align: center">
                        <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                        <h4 class="text-danger fw-bold">Are you sure you want to cancel this Order?</h4>
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
                        Verify & Cancel
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>
<!-- new form end -->

<form action="{{ route('advConvertToInvoice') }}" method="POST" class="needs-validation" novalidate>
    @csrf
    <div class="modal fade" id="convertModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Convert to Invoice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="convertID" id="convertID" hidden>
                    Are you sure you want to convert this into an invoice?
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

<script>
    $("#all_check").on("click", function() {
        if ($(this).prop("checked") == true) {
            $(".all_check").prop("checked", true)
        } else {
            $(".all_check").prop("checked", false)
        }
    });


    $(document).on("click", ".change_status", function() {

        $("#id").val($(this).val())
        $("#status").val($(this).data("status"))
        $("#modalId").modal("show")
    });

    $(document).on("click", ".cancel_status", function() {
        $("#cancelID").val($(this).val())
        $("#cancelOrderModal").modal("show")
    });

    $(document).on("click", ".convertToInvoice", function() {
        $("#convertID").val($(this).val())
        $("#convertModal").modal("show")
    });

  


   


    // SEND OTP
    $("#sendOtpBtn").on("click", function() {

        $.ajax({
            url: "{{ route('sendCancelAdvanceOTP') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#sendOtpBtn").attr("disabled", true);
                $("#sendOtpBtn").text("Sending OTP...");
            },
            success: function(res) {

                if (res.status) {

                    $("#otpSection").removeClass("d-none");
                    $("#verifyOtpBtn").removeClass("d-none");
                    $("#btnSection").addClass("d-none");

                    toastr.success(res.message);

                } else {
                    toastr.error(res.message, "Error");
                }
            },
            complete: function() {
                $("#sendOtpBtn").removeAttr("disabled");
                $("#sendOtpBtn").text("Send OTP");
            }
        });
    });


    // VERIFY OTP
    $("#verifyOtpBtn").on("click", function() {

        let otp = $("#otp").val().trim();

        if (otp === "") {
            toastr.warning("Please enter OTP first", "Warning");
            return;
        }

        $.ajax({
            url: "{{ route('verifyCancelAdvanceOrderOTP') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                otp: otp
            },
            beforeSend: function() {
                $("#verifyOtpBtn").attr("disabled", true);
                $("#verifyOtpBtn").text("Verifying...");
            },
            success: function(res) {

                if (!res.status) {
                    toastr.error(res.message);
                    return;
                }

                toastr.success("OTP Verified Successfully. Cancelling order...", "Success");

                // slight delay for UX
                setTimeout(function() {
                    $("#cancelOrderForm").submit();
                }, 1000);

            },
            complete: function() {
                $("#verifyOtpBtn").removeAttr("disabled");
                $("#verifyOtpBtn").text("Verify & Cancel");
            }
        });
    });
</script>
@endsection