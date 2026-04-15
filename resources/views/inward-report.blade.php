@extends('layouts.main')
@section('main-section')
    <style>
        .text-wrap-custom {
            white-space: normal !important;
            word-break: break-word;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">
                <h4>Inward Report</h4>
            </div>
            <div>
                <form action="" class="d-flex">
                    <div>
                        <label for="">From </label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') }}">
                    </div>
                    <div class="mx-2">
                        <label for="">To </label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') }}">
                    </div>

                </form>
            </div>
            <div>

            </div>


        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>PO</th>
                        <th>Vendor</th>

                        <th>PE No</th>
                        <th>Invoice</th>
                        <th>Invoice Date</th>
                        <th>R.M Date</th>

                        <th>User</th>
                        <th>Created at</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock_inward_mst as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td class="text-wrap-custom">{{ $item->po_name }}</td>
                            <td class="text-wrap-custom">{{ $item->vendor }}</td>


                            <td>{{ $item->invoice_id }}</td>
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->received_material_date }}</td>

                            <td class="text-wrap-custom">{{ $item->user }}</td>
                            <td class="text-wrap-custom"> {{ $item->created_at }}</td>
                            <td>
                                @if ($item->status == 'cancel')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-success">Complete</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="/inward-report-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                                @if ($item->status != 'cancel')
                                    <button class="btn btn-danger btn-sm btnCancelInvoice" type="button"
                                        value="{{ $item->id }}">Cancel</button>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
    <form action="{{ route('cancelPurchaseInvoice') }}" method="POST" id="cancelInvoiceForm">
        @csrf

        <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Cancel Purchase Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                            <h4 class="text-danger fw-bold">Are you sure you want to cancel this purchase invoice?</h4>


                            <input type="hidden" name="id" id="cancelID" hidden>
                        </div>
                        <div>

                            <p class="text-danger fw-bold mt-3">
                                ⚠️ Attention: Please read the following instructions carefully before proceeding. This
                                action will directly impact your stock records.
                            </p>
                            <ol class="text-danger">
                                <li>This action is irreversible.</li>
                                <li>Stock will be deducted for all items in this purchase.</li>
                                <li>Stock may become negative if sufficient quantity is not available.</li>
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
                            Verify & Cancel Invoice
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $(document).on("click", ".btnCancelInvoice", function() {
                $("#cancelID").val($(this).val())
                $("#cancelInvoiceModal").modal("show")
            });

            let otp = null;
            $("#sendOtpBtn").on("click", function() {


                $.ajax({
                    url: '/cancelPurchaseInvoiceOTP',
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
    </script>
@endsection
