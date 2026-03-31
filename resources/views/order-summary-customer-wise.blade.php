@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Summary</title>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Summary </h4>


            </div>
            <div class="d-flex">
                <div class="mx-2">
                    <form action="">
                        <select class="form-control" name="id" onchange="this.form.submit()">
                            <option value="">Select Department</option>

                            @foreach ($department as $item)
                                <option value="{{ $item->id }}" {{ request('id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach

                        </select>

                    </form>
                </div>
                <button id="exportToExcel" data-name="order summary" class="btn btn-success float-end btn-sm">Export to
                    Excel</button>
                    


                <form method="get" class="mx-4 d-flex">
                    <input type="hidden" name="id" value="{{ request('id') }}">
                    <a class="btn btn-info"
                        href="/order-summary-customer-wise?id={{ request('id') }}&date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                        << </a>
                            <input type="date" name="date" onchange="this.form.submit()" value="{{ request('date') }}"
                                class="form-control mx-2">

                            <a class="btn btn-info"
                                href="/order-summary-customer-wise?id={{ request('id') }}&date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                >>
                            </a>

                </form>
                <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body">

            <div id="PrintOrder" class="mt-5">
                <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                    <div>
                        <h3>{{ $setting->company_name }}</h3>
                        <p>{!! $setting->address !!}
                            <br>
                            E-Mail : {{ $setting->email }} <br>
                            Phone : {{ $setting->number }} <br>
                            GST : {{ $setting->gst_no }}

                        </p>


                    </div>


                    <div>
                        <div style="text-align: right;">
                            @if ($department_details)
                                <h4>{{ $department_details->name }}</h4>
                                <p>
                                    {{ $department_details->contact_person }} <br>
                                    {{ $department_details->number }} <br>
                                    Delivery Date : {{ request('date') }} <br>





                                </p>
                            @else
                                <h4>No Order Found</h4>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <hr>

                    @php
                        $sno = 1;
                    @endphp
                    <table style="width: 100%" id="exportTable">
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
                                            if ($qtySum == 0) {
                                                $qtySumName = '';
                                            } else {
                                                $qtySumName = $qtySum;
                                            }
                                            echo '<td style="border: solid 1px; padding: 5px">' . $qtySumName . '</td>';
                                            $total += $qtySum;
                                        }

                                        echo '<td style="border: solid 1px; padding: 5px">' . $total . '</td>';
                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
                <div class="d-flex mt-4 justify-content-between">
                    <div>
                        <p><b><u><i>Terms & Conditions</i></u></b></p>
                        <ol style="list-style:number;">


                        </ol>
                    </div>
                    <div>
                        <h6 class="float-end">For {{ $setting->company_name }}</h6>

                        <p class="mt-5">Authorized Signatory</p>
                    </div>

                </div>
            </div>


        </div>

    </div>
@endsection
