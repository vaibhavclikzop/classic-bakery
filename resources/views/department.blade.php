@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Department</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Department</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Department</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Contact Person</th>
                        <th> Number</th>
                        <th> Total Product</th>


                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($department as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td>{{ $item->name }}</td>
                            <td>{{ $item->contact_person }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->total_products }}</td>



                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}" data-contact_person="{{ $item->contact_person }}"
                                    data-number="{{ $item->number }}"><i class="fa fa-pencil"
                                        aria-hidden="true"></i></button>
                                <a class="btn btn-sm btn-success" href="/department-product/{{ $item->id }}"> <i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                                @if ($item->total_products == 0)
                                    <button class="btn btn-danger btn-sm btnDeleteDepartment" value="{{$item->id}}" type="button"><i
                                            class="fa fa-trash" aria-hidden="true"></i></button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveDepartment') }}">
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


                        <div class="col-md-12">
                            <label for="">Department Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-12 mt-4">
                            <label for="">Contact Person</label>
                            <input type="text" name="contact_person" id="contact_person" class="form-control">

                        </div>
                        <div class="col-md-12 mt-4">
                            <label for="">Number</label>
                            <input type="text" name="number" id="number" class="form-control">

                        </div>





                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <form action="{{ route('deleteDepartment') }}" method="POST" id="cancelInvoiceForm">
        @csrf

        <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Delete Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                            <h4 class="text-danger fw-bold">Are you sure you want to delete this department?</h4>
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
                            Verify & Delete Department
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#contact_person").val($(this).data("contact_person"));
            $("#number").val($(this).data("number"));
            $("#modal_name").text("Update Department");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Department");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $(document).ready(function() {
            $(document).on("click", ".btnDeleteDepartment", function() {
                $("#cancelID").val($(this).val())
                $("#cancelInvoiceModal").modal("show")
            });

            let otp = null;
            $("#sendOtpBtn").on("click", function() {


                $.ajax({
                    url: '/sendDeleteDepartmentOTP',
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
                        $("#verifyOtpBtn").text("Verify & Delete Department");

                    },
                });

            })
        });
    </script>
@endsection
