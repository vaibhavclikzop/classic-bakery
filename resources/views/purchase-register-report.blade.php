@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Purchase Register</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Register</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
                    </div>

                    <div>
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>
                </form>

            </div>
            <div>
                <button id="exportToExcel" data-name="purchase Register report"
                    class="btn btn-success float-end   mx-2">Export
                    to Excel</button>

                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="page-title">
                <h4>Purchase Register</h4>
            </div>
            <table class="table" id="exportTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Status</th>
                        <th>Supplier</th>
                        <th>PE NO.</th>
                        <th>PE Date</th>
                        <th>Taxable Amount</th>
                        <th>GST Amount</th>
                        <th>CESS Amount</th>
                        <th>TCS</th>
                        <th>TDS</th>
                        <th>Extra Charges</th>
                        <th>Invoice Amount</th>


                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        @php
                            if ($item->status == 'cancel') {
                                $item->taxable_amount = 0;
                                $item->gst_amount = 0;
                                $item->cess_amount = 0;
                                $item->delivery_charges = 0;
                                $item->total_amount = 0;
                            }

                        @endphp
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>
                                @if ($item->status == 'cancel')
                                    <span class="badge bg-danger">Cancel</span>
                                @else
                                    <span class="badge bg-success">Complete</span>
                                @endif
                            </td>
                            <td>{{ $item->vendor }}</td>
                            <td>{{ $item->invoice_id }}</td>
                            <td>{{ $item->received_material_date }}</td>
                            <td>{{ number_format($item->taxable_amount, 2) }}</td>
                            <td>{{ number_format($item->gst_amount, 2) }}</td>
                            <td>{{ number_format($item->cess_amount, 2) }}</td>
                            <td>0</td>
                            <td>0</td>
                            <td>{{ number_format($item->delivery_charges, 2) }}</td>
                            <td>{{ number_format($item->total_amount + $item->delivery_charges, 2) }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
