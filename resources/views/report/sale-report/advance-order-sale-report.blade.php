@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Advance Order Sale Report</title>
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
                <h4>Advance Order Sale Report</h4>
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
                        <th rowspan="2">Party</th>

                        @foreach ($pivot as $product => $row)
                            <th colspan="3" style="text-align:center">{{ $product }}</th>
                        @endforeach

                        <th colspan="3" style="text-align:center">Total</th>
                    </tr>

                    <tr>
                        @foreach ($pivot as $product => $row)
                            <th>Qty</th>
                            <th>Sale</th>
                            <th>Amount</th>
                        @endforeach

                        <th>Qty</th>
                        <th>Sale</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $columnTotals = [];
                        $grandTotalQty = 0;
                        $grandQty = 0;
                        $grandAmount = 0;
                    @endphp

                    @foreach ($parties as $party)
                        @php
                            $rowTotalQty = 0;
                            $rowQtyTotal = 0;
                            $rowAmountTotal = 0;
                        @endphp

                        <tr>
                            <td>{{ $party }}</td>

                            @foreach ($pivot as $product => $row)
                                @php
                                    $total_qty = $row[$party]['total_qty'] ?? 0;
                                    $qty = $row[$party]['qty'] ?? 0;
                                    $amount = $row[$party]['amount'] ?? 0;

                                    // Row totals
                                    $rowTotalQty += $total_qty;
                                    $rowQtyTotal += $qty;
                                    $rowAmountTotal += $amount;

                                    // Column totals
                                    $columnTotals[$product]['total_qty'] =
                                        ($columnTotals[$product]['total_qty'] ?? 0) + $total_qty;
                                    $columnTotals[$product]['qty'] = ($columnTotals[$product]['qty'] ?? 0) + $qty;
                                    $columnTotals[$product]['amount'] =
                                        ($columnTotals[$product]['amount'] ?? 0) + $amount;
                                @endphp

                                <td>{{ $total_qty }}</td>
                                <td>{{ $qty }}</td>
                                <td>{{ number_format($amount, 2) }}</td>
                            @endforeach

                            {{-- Row Total --}}
                            <td><strong>{{ $rowTotalQty }}</strong></td>
                            <td><strong>{{ $rowQtyTotal }}</strong></td>
                            <td><strong>{{ number_format($rowAmountTotal, 2) }}</strong></td>

                            @php
                                $grandTotalQty += $rowTotalQty;
                                $grandQty += $rowQtyTotal;
                                $grandAmount += $rowAmountTotal;
                            @endphp
                        </tr>
                    @endforeach

                    {{-- Grand Total Row --}}
                    <tr style="background:#f2f2f2; font-weight:bold;">
                        <td>Total</td>

                        @foreach ($pivot as $product => $row)
                            <td>{{ $columnTotals[$product]['total_qty'] ?? 0 }}</td>
                            <td>{{ $columnTotals[$product]['qty'] ?? 0 }}</td>
                            <td>{{ number_format($columnTotals[$product]['amount'] ?? 0, 2) }}</td>
                        @endforeach

                        <td>{{ $grandTotalQty }}</td>
                        <td>{{ $grandQty }}</td>
                        <td>{{ number_format($grandAmount, 2) }}</td>
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
