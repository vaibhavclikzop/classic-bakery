@extends('layouts.main')

@section('main-section')
    @push('title')
        <title>Sale Report User Wise</title>
    @endpush
    {{-- <style>
    tr,th,td{
        border: solid 1px;
        padding: 5px;
        font-size: 11px;
    }
</style> --}}

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <div class="page-title">
                <h4>Sale Report User Wise</h4>
            </div>
            <div>
                <button id="exportToExcel" data-name="sale register user wise" class="btn btn-success float-end btn-sm">Export
                    to
                    Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
            </div>

            <div>

                <form action="" class="d-flex">
                    <div>
                        <label for="">From Date</label>
                        <input type="date" name="fromDate" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDate') }}">
                    </div>
                    <div class="mx-2">
                        <label for="">To Date</label>
                        <input type="date" name="toDate" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDate') }}">
                    </div>
                    <div>
                        <label for="">Customer Type</label>
                        <select name="customer_type" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select Type</option>
                            <option value="customer" {{ request('customer_type') == 'customer' ? 'selected' : '' }}>Customer
                            </option>
                            <option value="outlet" {{ request('customer_type') == 'outlet' ? 'selected' : '' }}>Outlet
                            </option>

                        </select>
                    </div>
                    <div class="mx-2">
                        <label for="">User</label>
                        <select name="user_id" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select User</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request('user_id') ? 'selected' : '' }}>
                                    {{ $item->name }} </option>
                            @endforeach

                        </select>
                    </div>
                    
                </form>
            </div>

        </div>
        <div class="card-body" id="PrintOrder">
            <div class="text-center">
                <h4>Classic Bakery</h4>

            </div>
            <table class="" id="exportTable" style="width: 100%">
                <thead>
                    <tr>

                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">S.No</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Shop Name</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Inv. No.</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Invoice Date.</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Username</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Total</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">MRP</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">O Amt.</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">R Amt.</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Cash</th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Credit </th>
                        <th style="  border: solid 1px;padding: 5px;font-size: 11px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;">{{ $sno++ }}</th>
                            <th
                                style="
    border: 1px solid;
    padding: 5px;
    font-size: 11px;
    white-space: normal;
    word-break: break-word;
    width:150px
">
                                {{ $item->customer_name }}
                            </th>


                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;">{{ $item->order_no }}</th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;">{{ $item->invoice_date }}</th>
                            <th
                                style="    border: 1px solid;
    padding: 5px;
    font-size: 11px;
    white-space: normal;
    word-break: break-word;
    width:80px
">
                                {{ $item->user }}</th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;">
                                {{ formatQtyPrice($item->grand_total) }} </th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;">{{ formatQtyPrice($item->mrp) }}
                            </th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px; width:100px "></th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;; width:100px"></th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;; width:100px"></th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;; width:100px"></th>
                            <th style="  border: solid 1px;padding: 5px;font-size: 11px;; width:100px"></th>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>


    </div>
@endsection
