@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Purchase Variation</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Variation</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">Date</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') }}">
                    </div>

                    
                </form>

            </div>
            <div class="">
                <button id="exportToExcel" data-name="purchase variation report"
                    class="btn btn-success float-end   mx-2">Export
                    to Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="page-title">
                <h4>Purchase Variation</h4>
            </div>
            <table class="table dataTable" id="exportTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Supplier</th>
                        <th>Purchase/Invoice Date</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($filter as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->vendor }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ formatQtyPrice($item->price) }}</td>
                            <td>{{ formatQtyPrice($item->qty) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
