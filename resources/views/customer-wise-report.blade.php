@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Customer wise report</title>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Customer wise report </h4>
            </div>
            <div class="d-flex">



                <form method="get" class="mx-4 d-flex">
                    <input type="hidden" name="id" value="{{ request('id') }}">
                    <a class="btn btn-info"
                        href="?id={{ request('id') }}&date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                        << </a>
                            <input type="date" name="date" onchange="this.form.submit()" value="{{ request('date') }}"
                                class="form-control mx-2">

                            <a class="btn btn-info"
                                href="?id={{ request('id') }}&date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                >>
                            </a>

                </form>
                <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body">

            <div id="PrintOrder" class="mt-5">


                <div class="">
                    <hr>

                    @php
                        $sno = 1;
                    @endphp
                    <table style="width: 100%">
                        <thead>
                            <tr style="border: solid 1px; padding: 5px">
                                <th style="border: solid 1px; padding: 5px">S.No</th>
                                <th style="border: solid 1px; padding: 5px">Sub Category</th>
                                <th style="border: solid 1px; padding: 5px">Product</th>

                                @php
                                    $uniqueCustomers = collect($customers)->unique('customer'); // Remove duplicate customers
                                @endphp

                                @foreach ($uniqueCustomers as $value)
                                    <th style="border: solid 1px; padding: 5px">{{ $value->customer }}</th>
                                @endforeach

                                <th style="border: solid 1px; padding: 5px">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $sno = 1; @endphp
                            @foreach ($report as $row)
                                <tr style="border: solid 1px; padding: 5px">
                                    @php
                                        $total = 0;
                                        echo '<td style="border: solid 1px; padding: 5px">' . $sno++ . '</td>';
                                        echo '<td style="border: solid 1px; padding: 5px">' .
                                            $row[0]['sub_category'] .
                                            '</td>';
                                        echo '<td style="border: solid 1px; padding: 5px">' . $row[0]['name'] . '</td>';

                                        foreach ($uniqueCustomers as $customer) {
                                            $qtySum = 0;
                                            foreach ($row as $data) {
                                                if (
                                                    isset($data['customer']) &&
                                                    $data['customer'] == $customer->customer
                                                ) {
                                                    $qtySum += $data['qty']; // Sum all quantities for the same customer
                                                }
                                            }
                                            echo '<td style="border: solid 1px; padding: 5px">' . $qtySum . '</td>';
                                            $total += $qtySum;
                                        }

                                        echo '<td style="border: solid 1px; padding: 5px">' . $total . '</td>';
                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>

            </div>


        </div>

    </div>
@endsection
