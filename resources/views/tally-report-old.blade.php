@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Tally Report</title>
    @endpush
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            white-space: nowrap;
        }

        thead {
            background: #f2f2f2;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Tally Report</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>

                    <div>
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>
                </form>

            </div>
            <div>
                <button id="exportToExcel" data-name="TallyExport Report{{ Request('fromDt') }}{{ Request('toDt') }}"
                    class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body table-responsive" id="PrintOrder">

            <table id="exportTable">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="4" style="text-align: center">Export To Excel</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="4" style="text-align: center">Classic Bakery</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="4" style="text-align: center">
                            {{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Invoice Type</th>
                        <th>Date</th>
                        <th>Supplier Invoice No</th>
                        <th>Supplier Invoice Date</th>
                        <th>Invoice No</th>
                        <th>Ledger Name</th>
                        <th>Ledger Group Name</th>
                        <th>HSN/SAC</th>
                        <th>GST %</th>
                        <th>GSTIN No</th>
                        <th>Place of Supply</th>
                        <th>Amount</th>
                        <th>Invoice Amount</th>
                        <th>Narration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        @if ($row->status == 'cancel')
                            @php
                                $row->sub_total = 0;
                                $row->igst = 0;
                                $row->cgst = 0;
                                $row->sgst = 0;
                                $row->cess_amt = 0;
                            @endphp
                        @endif
                        {{-- Main invoice row --}}
                        <tr>
                            <td style="text-transform: capitalize">{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->name }}</td>
                            <td>

                                @if ($row->invoice_type == 'sales')
                                    Sundry Debtors
                                @else
                                    Sundry Creditors
                                @endif



                            </td>
                            <td></td>
                            <td></td>
                            <td>-</td>
                            <td>CHANDIGARH</td>
                            <td>{{ number_format($row->sub_total + $row->igst + $row->cess_amt  +($row->delivery_charges_final ?? 0), 2) }}</td>
                            <td>{{ round(($row->sub_total ?? 0) + ($row->igst ?? 0) + ($row->cess_amt ?? 0))   +($row->delivery_charges_final ?? 0) }}</td>

                            <td></td>
                        </tr>

                        {{-- Advance Orders / Sales Ledger --}}
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->order_type }}</td>
                            <td>
                                @if ($row->invoice_type == 'sales')
                                    Sales Accounts
                                @else
                                    Purchase Accounts
                                @endif

                            </td>
                            <td>-</td>
                            <td>{{ $row->gst }}</td>
                            <td></td>
                            <td></td>
                            <td>-{{ number_format($row->sub_total + $row->igst - $row->cgst - $row->sgst - $row->igst, 2) }}
                            </td>
                            <td></td>
                            <td></td>
                        </tr>

                        {{-- GST Breakdown --}}

                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>OUTPUT CGST {{ $row->cgst > 0 ? '9%' : '' }}</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format(-1 * $row->cgst, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>OUTPUT SGST {{ $row->sgst > 0 ? '9%' : '' }}</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format(-1 * $row->sgst, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>OUTPUT IGST {{ $row->igst > 0 ? $row->gst . '%' : '' }}</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format(-1 * $row->igst, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>


                        {{-- CESS --}}
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>CESS</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($row->cess_amt, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>

                        {{-- Discount / Delivery Charges / TCS / TDS --}}
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>Discount</td>
                            <td>Indirect Incomes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>0.00</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>Delivery Charges</td>
                            <td>Indirect Incomes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                @if ($row->delivery_charges > 0.0)
                               {{ number_format(-1 * $row->delivery_charges, 2) }}
                                @else
                                    0.00
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>TCS</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>0.00</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->id }}</td>
                            <td>TDS</td>
                            <td>Duties & Taxes</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>0.00</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

    </div>
@endsection
