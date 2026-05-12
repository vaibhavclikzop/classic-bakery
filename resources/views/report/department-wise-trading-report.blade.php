@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Department wise trading report</title>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Department wise trading report </h4>
            </div>

            <form method="get" class="mx-4 d-flex">


                <input type="date" name="date" value="{{ request('date') }}" class="form-control mx-2">
                <select name="customer_type" id="" class="form-control mx-2">
                    <option value="">Select</option>
                    <option value="customer" {{ request('customer_type') == 'customer' ? 'selected' : '' }}>Customer
                    </option>
                    <option value="outlet" {{ request('customer_type') == 'outlet' ? 'selected' : '' }}>Outlet</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>



            </form>
            <button id="exportToExcel" data-name="order summary treading items"
                class="btn btn-success float-end btn-sm">Export
                to
                Excel</button>
            <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                    aria-hidden="true"></i> Print</button>

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
                        {{-- <div style="text-align: right;">
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
                        </div> --}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table style="width: 100%" id="exportTable">
                        <thead>
                            <tr>
                                <th style="border: solid 1px; padding: 5px">S.No</th>
                                <th style="border: solid 1px; padding: 5px">Product</th>

                                @foreach ($customers as $cust)
                                    <th style="white-space: normal; border: solid 1px; padding: 5px">
                                        {{ $cust->outlet_name }}
                                    </th>
                                @endforeach

                                <th style="border: solid 1px; padding: 5px">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $index => $row)
                                <tr>
                                    <td style="border: solid 1px; padding: 5px">{{ $index + 1 }}</td>
                                    <td style="border: solid 1px; padding: 5px">{{ $row->product }}</td>

                                    @foreach ($customers as $cust)
                                        @php
                                            $colName = preg_replace('/[^A-Za-z0-9_]/', '_', $cust->outlet_name);
                                        @endphp

                                        <td style=" border: solid 1px; padding: 5px">
                                            {{ formatQtyPrice($row->$colName) ?? 0 }}
                                        </td>
                                    @endforeach

                                    <td style="text-center; border: solid 1px; padding: 5px">
                                        {{ formatQtyPrice($row->total_qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>


        </div>

    </div>
@endsection
