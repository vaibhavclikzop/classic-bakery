@extends('layouts.main')
@section('main-section')
    @push('title')
        <title> Barcode</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Barcode</h4>
            </div>
        </div>

        <div class="barcode-content-list">
            <form class="row gx-3 gy-2 align-items-center mt-0" method="GET" action="{{ route('barcode') }}" id="filter-form">
                <div class="col-sm-3 mb-3">
                    <label class="form-label" for="">Delivery Date<span class="text-danger ms-1">*</span></label>
                    <input type="date" id="delivery_date" name="delivery_date" value="{{ request('delivery_date') }}"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">--Select category--</option>
                        @foreach ($f_product_category as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-3">
                    <label for="">Sub Category</label>
                    <select id="sub_category_id" name="sub_category_id" class="form-control" required>
                        <option value="">--Select Sub Category--</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="">Product Name</label>
                    <select id="product_id" name="product_id" class="form-control" required>
                        <option value="">--Select Products--</option>
                    </select>
                </div>

                <div class="col-auto mb-4 ">
                    <div class="form-check fs-15 mt-2">
                        <input class="form-check-input" type="checkbox" id="checkItem" name="from_order" value="add">
                        <label class="form-check-label" for="checkItem">Load item from order</label>
                    </div>
                </div>
                <div class="col-auto mb-4">
                    <button type="button" class="btn btn-submit btn-primary me-2 mt-0"
                        onclick="document.getElementById('filter-form').submit();">
                        <span><i class="fas fa-eye me-1"></i></span>Load Items
                    </button>
                </div>
            </form>
          
            <div class="col-lg-12">
               
                <div class="table-responsive rounded border">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Item Name</th>
                                <th>Qty</th>
                                <th>Expiry Date</th>
                                <th> Barcode Qty</th>
                                <th> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($productNames as $item)
                                <form action="{{ route('print-barcode', ['id' => $item->product_id]) }}" method="GET"
                                    target="_blank">
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format(optional($item)->qty ?? 0, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse(request('delivery_date'))->addDays($item->expiry)->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            <div class="product-quantity border-secondary-transparent">
                                                <span class="quantity-btn"><i data-feather="minus-circle"
                                                        class="feather-search"></i></span>
                                                <input type="text" class="quntity-input"
                                                    value="{{ number_format(optional($item)->qty ?? 0, 2) }}"
                                                    name="qty">
                                                <span class="quantity-btn">+<i data-feather="plus-circle"
                                                        class="plus-circle"></i></span>
                                            </div>
                                        </td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <input type="hidden" name="expiry"
                                                    value="{{ \Carbon\Carbon::parse(request('delivery_date'))->addDays($item->expiry)->format('Y-m-d') }}">
                                                <button type="submit" class="btn  btn-secondary me-2 mt-0 print-btn">
                                                    <span><i class="fas fa-print me-1"></i></span>Print
                                                </button>

                                            </div>

                                        </td>


                                    </tr>
                                </form>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>


        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#product_id").select2();
            $("#sub_category_id").select2();
        });

         $("#category_id").on("change", function() {
            $.ajax({
                url: "/GetFinishSubCategory",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Sub Category----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#sub_category_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });

         $("#sub_category_id").on("change", function() {
            $.ajax({
                url: "/GetProductFinish",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    console.log(result);
                    var html = "";
                    html += '<option value="">----Select Products----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#product_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });
    </script>
@endsection
