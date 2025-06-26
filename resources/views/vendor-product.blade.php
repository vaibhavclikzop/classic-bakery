@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Vendor Product</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4 class="">Vendor Product List <br> </h4>
                <span>Vendor Name : {{ $vendor->name }}</span> <br>
                <span>Vendor Email : {{ $vendor->email }}</span> <br>
                <span>Vendor Contact : {{ $vendor->number }}</span> <br>

            </div>
            <div class="">


                <button type="button" class="btn btn-dark" id="AddProduct">Add Product</button>

            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('UpdateVendorPrice') }}" method="POST">
                @csrf
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.no</th>

                            <th>Brand Name</th>
                            <th> Category</th>
                            <th>Product Name</th>
                            <th>Article Code</th>
                            <th>Price</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($vendor_product as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->brand }}</td>
                                <td>{{ $item->category }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->article_no }}</td>
                                <td>
                                    <input type="number" step="0.01" name="price[{{ $item->id }}][]"
                                        value="{{ $item->price }}" class="form-control">
                                </td>
                                <td>

                                    @php
                                        $checked = '';
                                        if ($item->active == 1) {
                                            $checked = 'checked';
                                        }

                                    @endphp

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" value="{{ $item->id }}" type="checkbox"
                                            role="switch" {{ $checked }}>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
                <div class="col-md-12 text-center">
                    <button class="btn btn-primary" type="submit">Update Price</button>
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade" id="modalId">
        <div class="modal-dialog  modal-dialog-scrollable modal-xl">
            <form method="POST" action="{{ route('AllocateProduct') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Products
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table thisDataTable">
                            <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">

                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th><input type="checkbox" class="product_id" id="selectall"></th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>

                                    <th>Product</th>
                                    <th>Article Code</th>
                                    <th>Min Stock</th>
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
                                        <td>{{ $item->brand }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->sub_category }}</td>

                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->article_no }}</td>
                                        <td>{{ $item->min_stock }}</td>
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
    <script>
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
            $(".thisDataTable").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "ordering": false, // Disable ordering for all columns
                "buttons": ["excel", 'csv'],
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            }).buttons().container().appendTo('.col-md-6:eq()');

        });
        $(document).on("click", ".form-check-input", function() {
            var active = 0;
            var id = $(this).val();
            if ($(this).prop("checked")) {
                active = 1;
            }


            $.ajax({
                url: "/updateVendorProduct",
                type: "POST",
                data: {
                    active: active,
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {

                    if (result.error == true) {
                        toastr.success(result.msg)
                    }

                },
                error: function(result) {
                    let message = "Something went wrong!";

                    // Try to extract message from server response
                    if (result.responseJSON && result.responseJSON.msg) {
                        message = result.responseJSON.msg;
                    }

                    // Show error using Toastr
                    toastr.error(message, "Error");
                }
            });
        })
    </script>
@endsection
