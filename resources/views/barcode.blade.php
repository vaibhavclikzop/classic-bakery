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

                <div class="col-sm-3 mb-3">
                    <label class="form-label">Product Name<span class="text-danger ms-1">*</span></label>
                    <select class="form-control" id="product_id" name="product_id">
                        <option value="">Select Item</option>
                        @foreach ($data as $item)
                            <option value="{{ $item->id }}" data-product="{{ $item->name }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto ">
                    <div class="form-check fs-15 mt-2">
                        <input class="form-check-input" type="checkbox" id="checkItem" name="from_order" value="add"
                            >
                        <label class="form-check-label" for="checkItem">Load item from order</label>
                    </div>
                </div>
                <div class="col-auto">
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
                                                    value="{{ number_format(optional($item)->qty ?? 0, 2) }}" name="qty">
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
        });
    </script>
@endsection
