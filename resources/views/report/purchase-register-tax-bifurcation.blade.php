@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Purchase Register GST Bifurcation</title>
    @endpush
    <style>

    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Register GST Bifurcation</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt')}}">

                    </div>

                    <div class="mx-2">
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>
                    {{-- 
                    <div>
                        <label for="">Customer Type</label>
                        <select name="customer_type" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select</option>
                            <option value="customer" {{ request('customer_type') == 'customer' ? 'selected' : '' }}>Customer
                            </option>
                            <option value="outlet" {{ request('customer_type') == 'outlet' ? 'selected' : '' }}>Outlet
                            </option>
                        </select>

                    </div> --}}
                </form>

            </div>
            <div>
                <button id="exportToExcel" data-name="sale report gst bifurcation"
                    class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body table-responsive" id="PrintOrder">
            <div class="page-title">
                <h4>Purchase Register</h4>
            </div>
            <table class="myTable " id="exportTable">
                <thead>
                    <tr>
                        <th style="border: solid 1px;padding: 5px;">Status.</th>
                        <th style="border: solid 1px;padding: 5px;">Invoice No.</th>
                        <th style="border: solid 1px;padding: 5px;">Supplier Invoice Date</th>
                        <th style="border: solid 1px;padding: 5px;">Vendor Name</th>
                        @foreach ($gstRates as $gst)
                            <th style="border: solid 1px;padding: 5px;">Taxable {{ $gst }}%</th>
                            <th style="border: solid 1px;padding: 5px;">GST {{ $gst }}%</th>
                        @endforeach
                        <th style="border: solid 1px;padding: 5px;">Total GST</th>
                        <th style="border: solid 1px;padding: 5px;">Delivery Charges</th>
                        <th style="border: solid 1px;padding: 5px;">Total Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td style="border: solid 1px;padding: 5px;">

                                   @if ($item->status == 'cancel')
                                    <span class="badge bg-danger">Cancel</span>
                                @else
                                    <span class="badge bg-success">Complete</span>
                                @endif
                            </td>
                            <td style="border: solid 1px;padding: 5px;">{{ $item->invoice_id }}</td>
                            <td style="border: solid 1px;padding: 5px;">{{ $item->invoice_date }}</td>
                            <td style="border: solid 1px;padding: 5px;">{{ $item->vendor }}</td>
                            @foreach ($gstRates as $gst)
                                {{-- TAXABLE --}}
                                <td style="border: solid 1px;padding: 5px;">
                                    {{ number_format($item->{'taxable_' . $gst} ?? 0, 2) }}
                                </td style="border: solid 1px;padding: 5px;">

                                {{-- GST --}}
                                <td style="border: solid 1px;padding: 5px;">
                                    {{ number_format($item->{'gst_' . $gst} ?? 0, 2) }}
                                </td>
                            @endforeach

                            {{-- TOTAL GST --}}
                            <td style="border: solid 1px;padding: 5px;">
                                {{ number_format($item->total_gst ?? 0, 2) }}
                            </td>

                            {{-- TOTAL AMOUNT --}}
                             <td style="border: solid 1px;padding: 5px;">
                                {{ number_format($item->delivery_charges ?? 0, 2) }}
                            </td>
                            <td style="border: solid 1px;padding: 5px;">
                                {{ number_format($item->grand_total + $item->delivery_charges ?? 0, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>
    </div>
@endsection
