@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Category wise sale and damage</title>
    @endpush
    <style>
        tr,
        th,
        td {
            border: solid 1px;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Category wise sale and damage</h4>
            </div>
            <div class="mt-2" style="display: flex;justify-content: space-between">

                <div>
                    <form action="">
                        <div style="display:flex">
                            <div>
                                <label for="">From Date</label>
                                <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                            </div>
                            <div class="mx-3">
                                <label for="">To Date</label>
                                <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                            </div>
                            <div class="col-2">
                                <label for="">Order Type</label>
                                <select name="order_type" id="order_type" class="form-control">
                                    <option value="">Select Order Type</option>
                                    @foreach ($order_type as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('order_type') == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mx-3 col-3">
                                <label for="">Sub Category</label>
                                <select name="f_product_sub_category" id="f_product_sub_category" class="form-control">
                                    <option value="">Select Order Type</option>
                                    @foreach ($f_product_sub_category as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('f_product_sub_category') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="">Search</label> <br>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <button id="exportToExcel" data-name="Category wise sale and damage report"
                        class="btn btn-success float-end btn-sm mx-2">Export
                        to Excel</button>

                </div>
            </div>

        </div>
        <div class="card-body table-responsive">

            <table class="w-100" id="exportTable" border="1" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="{{ 4 + count($parties) * 4 + 4 }}" style="text-align: center">
                            From Date : {{ request('fromDt') }} To Date : {{ request('toDt') }}
                            <br>
                            <span id="orderSubCategoryName"></span>
                        </th>
                    </tr>

                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2" style="min-width: 150px">Category</th>
                        <th rowspan="2" style="min-width: 200px">Sub Category</th>
                        <th rowspan="2" style="min-width: 200px">Name</th>

                        @foreach ($parties as $party)
                            <th colspan="4" style="text-align: center">{{ $party }}</th>
                        @endforeach

                        <th colspan="4" style="text-align:center">Total</th>
                    </tr>

                    <tr>
                        @foreach ($parties as $party)
                            <th>Order</th>
                            <th>Sale</th>
                            <th>Amount</th>
                            <th>Return</th>
                        @endforeach

                        <th>Order</th>
                        <th>Sale</th>
                        <th>Amount</th>
                        <th>Return</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $colOrder = $colSale = $colAmount = $colReturn = [];

                        foreach ($parties as $party) {
                            $safe = preg_replace('/[^A-Za-z0-9]/', '_', $party);
                            $colOrder[$safe] = 0;
                            $colSale[$safe] = 0;
                            $colAmount[$safe] = 0;
                            $colReturn[$safe] = 0;
                        }

                        $grandOrder = 0;
                        $grandSale = 0;
                        $grandAmount = 0;
                        $grandReturn = 0;
                    @endphp

                    @foreach ($data as $key => $row)
                        @php
                            $rowOrder = 0;
                            $rowSale = 0;
                            $rowAmount = 0;
                            $rowReturn = 0;
                        @endphp

                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ $row['sub_category'] }}</td>
                            <td>{{ $row['product'] }}</td>

                            @foreach ($parties as $party)
                                @php
                                    $safe = preg_replace('/[^A-Za-z0-9]/', '_', $party);

                                    $order = $row['order_qty_' . $safe] ?? 0;
                                    $sale = $row['sale_qty_' . $safe] ?? 0;
                                    $amount = $row['amount_' . $safe] ?? 0;
                                    $return = $row['return_qty_' . $safe] ?? 0;

                                    // ✅ FIX: no multiplication
                                    $totalAmount = $amount;

                                    // Row totals
                                    $rowOrder += $order;
                                    $rowSale += $sale;
                                    $rowAmount += $totalAmount;
                                    $rowReturn += $return;

                                    // Column totals
                                    $colOrder[$safe] += $order;
                                    $colSale[$safe] += $sale;
                                    $colAmount[$safe] += $totalAmount;
                                    $colReturn[$safe] += $return;
                                @endphp

                                <td>{{ formatQtyPrice($order) }}</td>
                                <td>{{ formatQtyPrice($sale) }}</td>
                                <td>{{ formatQtyPrice($totalAmount) }}</td>
                                <td>{{ formatQtyPrice($return) }}</td>
                            @endforeach

                            {{-- Row Total --}}
                            <td>{{ formatQtyPrice($rowOrder) }}</td>
                            <td>{{ formatQtyPrice($rowSale) }}</td>
                            <td>{{ formatQtyPrice($rowAmount) }}</td>
                            <td>{{ formatQtyPrice($rowReturn) }}</td>
                        </tr>

                        @php
                            $grandOrder += $rowOrder;
                            $grandSale += $rowSale;
                            $grandAmount += $rowAmount;
                            $grandReturn += $rowReturn;
                        @endphp
                    @endforeach

                    {{-- Footer --}}
                    <tr style="font-weight:bold; background:#f2f2f2;">
                        <td colspan="4" style="text-align:right;">Total</td>

                        @foreach ($parties as $party)
                            @php $safe = preg_replace('/[^A-Za-z0-9]/', '_', $party); @endphp

                            <td>{{ formatQtyPrice($colOrder[$safe]) }}</td>
                            <td>{{ formatQtyPrice($colSale[$safe]) }}</td>
                            <td>{{ formatQtyPrice($colAmount[$safe]) }}</td>
                            <td>{{ formatQtyPrice($colReturn[$safe]) }}</td>
                        @endforeach

                        <td>{{ formatQtyPrice($grandOrder) }}</td>
                        <td>{{ formatQtyPrice($grandSale) }}</td>
                        <td>{{ formatQtyPrice($grandAmount) }}</td>
                        <td>{{ formatQtyPrice($grandReturn) }}</td>
                    </tr>
                </tbody>
            </table>


        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("select").select2();




            let order_type = $("#order_type option:selected").text();
            let f_product_sub_category = $("#f_product_sub_category option:selected").text();

            $("#orderSubCategoryName").text("Order Type : " + order_type + " Sub Category : " +
                f_product_sub_category);

        })
    </script>
@endsection
