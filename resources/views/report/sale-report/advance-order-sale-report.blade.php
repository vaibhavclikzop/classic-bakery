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
                        <th rowspan="2">Name</th>

                        @foreach ($parties as $party)
                            <th colspan="3" style="text-align:center">{{ $party }}</th>
                        @endforeach
                    </tr>

                    <tr>
                        @foreach ($parties as $party)
                            <th>Order</th>
                            <th>Sale</th>
                            <th>Amount</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pivot as $product => $row)
                        <tr>
                            <td>{{ $product }}</td>

                            @foreach ($parties as $party)
                                @php
                                    $qty = $row[$party]['qty'] ?? 0;
                                    $amount = $row[$party]['amount'] ?? 0;
                                @endphp

                                <td>{{ $qty }}</td>
                                <td>{{ $qty }}</td>
                                <td>{{ number_format($amount, 2) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
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
