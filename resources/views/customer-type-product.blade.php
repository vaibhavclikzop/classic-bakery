@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Customer type product list </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4 class="">Customer type <br>product list <br> </h4>
                <span> Name : {{ $customer_type->name }}</span> <br>



            </div>
            <div>
                <form action="">
                    <div>
                        <select name="sub_category_id" id="sub_category_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Select All Sub Category</option>
                            @foreach ($sub_category as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('sub_category_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                </form>
                <form action="{{ route('UpdateAllMargin') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" value="{{ request('id') }}" name="customer_type_id">
                    <input type="hidden" value="{{ request('sub_category_id') }}" name="sub_category_id">
                    <div class="d-flex mt-4">
                        <div>
                            <input type="number" step="0.01" name="margin" class="form-control" required
                                placeholder="Enter Margin in percentage">
                        </div>
                        <div>
                            <button type="submit" name="btnUpdateAll" value="btnUpdateAll" class="btn btn-primary">Apply to
                                All</button>

                        </div>
                    </div>
                </form>
            </div>
            <div class="">


                <button type="button" class="btn btn-dark" id="AddProduct">Add Product</button>
                <button class="btn btn-danger btnUnAllocate" type="button">UnAllocate</button>

            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('UpdateCustomerTypePrice') }}" method="post">
                @csrf
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>
                                <input type="checkbox" id="checks">
                            </th>

                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Product Name</th>
                            <th>Article</th>
                            <th>MRP</th>
                            <th>Margin (%)</th>
                            <th>Sale Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($customer_type_product as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td><input type="checkbox" name="checks[]" value="{{ $item->id }}" class="checks"></td>

                                <td>{{ $item->category }}</td>
                                <td style="word-wrap: break-word; white-space: normal;">{{ $item->sub_category }}</td>
                                <td style="word-wrap: break-word; white-space: normal;">
                                    {{ $item->name }}
                                </td>

                                <td>{{ $item->article_no }}</td>
                                <td>{{ $item->price }}</td>
                                <td>
                                    <input type="number" step="0.01" class="form-control margin"
                                        name="margin[{{ $item->id }}][]" value="{{ $item->margin }}"
                                        data-mrp="{{ $item->price }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control sale_price"
                                        name="sale_price[{{ $item->id }}][]" value="{{ $item->sale_price }}"
                                        data-mrp="{{ $item->price }}">

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade" id="modalId">
        <div class="modal-dialog  modal-dialog-scrollable modal-xl">
            <form method="POST" action="{{ route('AllocateCustomerTypeProduct') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Products
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-3 float-end">
                            <select id="subcategoryFilter" class="form-control">
                                <option value="">All Subcategories</option>
                                @foreach ($products->pluck('sub_category')->unique() as $subcategory)
                                    <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                                @endforeach
                            </select>

                        </div>
                        <table class="table MydataTable">
                            <input type="hidden" name="customer_type_id" value="{{ $customer_type->id }}">

                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th><input type="checkbox" class="product_id" id="selectall"></th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Name</th>
                                    <th>Article No</th>

                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($products as $item)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td><input type="checkbox" class="checks" name="product_id[]"
                                                value="{{ $item->id }}"></td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->sub_category }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->article_no }}</td>

                                        <td>{{ $item->price }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form action="{{ route('unAllocateProducts') }}" method="POST" id="unAllocateProducts">
        @csrf

        <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">UnAllocate Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div style="text-align: center">
                            <span style="font-size: 24px; color: red"><i class="fa-solid fa-ban"></i></span>
                            <h4 class="text-danger fw-bold">Are you sure you want to un-allocate product?</h4>
                            <input type="" name="id" id="productIDS" hidden>
                        </div>
                        {{-- <div class="mb-3 mt-3" id="btnSection">
                            <button type="button" class="btn btn-dark w-100" id="sendOtpBtn">
                                Send OTP
                            </button>
                        </div>

                        <!-- OTP Input -->
                        <div class=" mt-3 d-none" id="otpSection">
                            <label>Enter OTP</label>
                            <input type="text" id="otp" class="form-control" placeholder="Enter OTP">
                        </div> --}}
                         <button type="submit" class="btn btn-danger mt-3 w-100" id="verifyOtpBtn" type="button">
                            UnAllocate
                        </button>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger d-none w-100" id="verifyOtpBtn" type="button">
                            Verify & UnAllocate
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $("#sub_category_id").select2()
            $("#subcategoryFilter").select2();
        })
        $("#AddProduct").on("click", function() {


            $("#modalId").modal("show");
        })
        $(document).ready(function() {
            $('#selectall').on('click', function() {
                if ($(this).prop("checked")) {
                    $(".checks").prop("checked", true)
                } else {
                    $(".checks").prop("checked", false)
                }
            });
        });

        $(document).on("keyup", ".sale_price", function() {
            var sale_price = parseFloat($(this).val()) || 0;
            var mrp = parseFloat($(this).data("mrp")) || 0;
            if (mrp > 0) {
                var margin = ((mrp - sale_price) / mrp) * 100;
                margin = margin.toFixed(2); // Keep only 2 decimal places

                // Find the corresponding margin input and update its value
                $(this).closest("tr").find(".margin").val(margin);
            }
        })
        $(document).on("keyup", ".margin", function() {
            var margin = parseFloat($(this).val()) || 0;
            var mrp = parseFloat($(this).data("mrp")) || 0;

            if (mrp > 0) {
                var sale_price = mrp - (mrp * (margin / 100)); // Correct formula to calculate sale_price
                sale_price = sale_price.toFixed(2); // Keep only 2 decimal places

                // Find the corresponding sale_price input and update its value
                $(this).closest("tr").find(".sale_price").val(sale_price);
            }
        });
        $(document).ready(function() {
            var table = $('.MydataTable').DataTable({
                "searching": true, // Enable searching
            });

            // Filter when subcategory changes
            $('#subcategoryFilter').on('change', function() {
                let subcategory = $(this).val();
                table.column(3).search(subcategory).draw(); // Column 3 is Subcategory
            });

            $("#checks").on("click", function() {
                if ($(this).prop("checked")) {
                    $(".checks").prop("checked", true);
                } else {
                    $(".checks").prop("checked", false);
                }

            });


        });
        $(document).ready(function() {

            $(document).on("click", ".btnUnAllocate", function() {
                if ($(".checks:checked").length === 0) {
                    alert("Please select at least one product");
                    return;
                }
                let ids = [];

                $(".checks:checked").each(function() {
                    ids.push($(this).val());
                });

                $("#productIDS").val(ids.join(","));
                $("#cancelInvoiceModal").modal("show")
            });

            let otp = null;
            $("#sendOtpBtn").on("click", function() {


                $.ajax({
                    url: '/sendUnAllocateOTP',
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
                            $("#unAllocateProducts").submit();
                        }

                    },
                    complete: function() {
                        $("#verifyOtpBtn").removeAttr("disabled");
                        $("#verifyOtpBtn").text("Verify & UnAllocate");

                    },
                });

            })
        });
    </script>
@endsection
