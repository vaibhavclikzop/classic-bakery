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

            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('UpdateCustomerTypePrice') }}" method="post">
                @csrf
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.no</th>


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

                                <td>{{ $item->category }}</td>
                                <td>{{ $item->sub_category }}</td>
                                <td>{{ $item->name }}</td>
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
        });
    </script>
@endsection
