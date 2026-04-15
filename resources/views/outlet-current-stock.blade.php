@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div>
                <div class="page-title">
                    <h4>Outlet Current Stock</h4>
                </div>
                <form method="GET" {{ route('current-stock') }}>
                    <div class="mt-4">

                        <select name="outlet_id" id="" required class="form-control" onchange="this.form.submit()">
                            <option value="">Select Outlet</option>
                            @foreach ($outlet as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('outlet_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->outlet_name }}</option>
                            @endforeach
                        </select>

                    </div>
                </form>
            </div>
            <div>
                <div>
                    @if ($totalDuplicates > 0)
                        <a class="btn btn-danger " href="?outlet_id={{ request('outlet_id') }}&duplicate=1">Duplicate
                            Products : {{ $totalDuplicates }}</a>

                        <button class="btn btn-success mx-3 btnSendDeleteOTP" type="button">Delete Duplicate</button>
                    @endif

                    <button class="btn btn-primary float-end btnSendOTP" type="button">Update Stock</button>
                </div>
            </div>
        </div>
        <div class="card-body" id="">
            <form action="{{ route('updateOutletStock') }}" method="post" id="submitForm">
                @csrf
                <input type="hidden" name="outlet_id" value="{{ request('outlet_id') }}" hidden>

                @php
                    $sno = 1;
                @endphp
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Product Name</th>


                            <th>Stock</th>
                            <th>Stock Adjust</th>
                            <th>Update at</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp

                        @foreach ($current_stock as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->stock }}</td>
                                <td> <input type="number" step="0.01" name="product_id[{{ $item->id }}][]"
                                        class="form-control" value="0"> </td>
                                <td>{{ $item->updated_at }}</td>
                            </tr>
                        @endforeach


                    </tbody>

                </table>
            </form>
        </div>

    </div>



    <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div style="text-align: center">
                        <span style="font-size: 24px; color: green"> <i class="fa fa-check-circle" aria-hidden="true"></i>
                        </span>
                        <h4 class="text-danger fw-bold">Are you sure you want to update stock?</h4>
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
                        Verify & Update Stock
                    </button>
                </div>

            </div>
        </div>
    </div>


    <form action="{{ route('deleteOutletCSDuplicate') }}" method="POST" id="deleteDuplicateForm">
        @csrf

        <div class="modal fade" id="deleteDuplicateModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Delete Duplicate Products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="outlet_id" value="{{ request('outlet_id') }}" hidden>

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"> <i class="fa fa-trash" aria-hidden="true"></i>
                            </span>
                            <h4 class="text-danger fw-bold">Are you sure you want to delete duplicate products?</h4>
                            <input type="hidden" name="id" id="cancelID" hidden>
                        </div>
                        <div class="mb-3 mt-3" id="btnSectionDuplicate">
                            <button type="button" class="btn btn-dark w-100" id="sendOtpBtnDuplicate">
                                Send OTP
                            </button>
                        </div>

                        <!-- OTP Input -->
                        <div class=" mt-3 d-none" id="otpSectionDuplicate">
                            <label>Enter OTP</label>
                            <input type="text" id="duplicateOTP" class="form-control" placeholder="Enter OTP">
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger d-none w-100" id="verifyOtpBtnDuplicate"
                            type="button">
                            Verify & Delete Duplicate
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $(document).on("click", ".btnSendOTP", function() {

                $("#cancelInvoiceModal").modal("show")
            })

            let otp = null;
            $("#sendOtpBtn").on("click", function() {



                $.ajax({
                    url: '/sendStockUpdateOTP',
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
                            $("#submitForm").submit();
                        }

                    },
                    complete: function() {
                        $("#verifyOtpBtn").removeAttr("disabled");
                        $("#verifyOtpBtn").text("Verify & Update Stock");

                    },
                });

            });

            $(document).on("click", ".btnSendDeleteOTP", function() {

                $("#deleteDuplicateModal").modal("show")
            })


            $("#sendOtpBtnDuplicate").on("click", function() {
                
                $.ajax({
                    url: '/sendDeleteDuplicateOTP',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#sendOtpBtnDuplicate").attr("disabled", "disabled");
                        $("#sendOtpBtnDuplicate").text("Sending OTP....");

                    },
                    success: function(res) {
                        let data = res;
                        if (data.status == true) {


                            let data = res;
                            otp = data.OTP;
                            $('#otpSectionDuplicate').removeClass('d-none');
                            $('#verifyOtpBtnDuplicate').removeClass('d-none');
                            $("#btnSectionDuplicate").addClass("d-none")
                            toastr.success(data.message)
                        } else {
                            toastr.error(data.message)
                        }
                    }
                });
            });

            $("#verifyOtpBtnDuplicate").on("click", function() {
                let clientOTP = $("#duplicateOTP").val().trim();
                $.ajax({
                    url: '/verifyCancelOTP',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        otp: clientOTP,
                    },
                    beforeSend: function() {
                        $("#verifyOtpBtnDuplicate").attr("disabled", "disabled");
                        $("#verifyOtpBtnDuplicate").text("Verifying OTP....");

                    },
                    success: function(res) {
                        if (res.status == false) {

                            toastr.error(res.message)
                            return;
                        } else {
                            $("#verifyOtpBtnDuplicate").attr("disabled", "disabled");
                            $("#verifyOtpBtnDuplicate").text("Verifying OTP....");
                            $("#deleteDuplicateForm").submit();
                        }

                    },
                    complete: function() {
                        $("#verifyOtpBtnDuplicate").removeAttr("disabled");
                        $("#verifyOtpBtnDuplicate").text("Verify & Delete Duplicate");

                    },
                });

            });
        })
    </script>
@endsection
