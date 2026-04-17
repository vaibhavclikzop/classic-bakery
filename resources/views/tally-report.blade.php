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
                <button id="exportToExcelTally" data-name="TallyExport Report{{ Request('fromDt') }}{{ Request('toDt') }}"
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
                        <tr>
                            <td>{{ $row->invoice_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->invoice_no }}</td>
                            <td>{{ $row->ledger }}</td>
                            <td>{{ $row->ledger_group }}</td>
                            <td>{{ $row->hsn ?? '' }}</td>
                            <td>{{ $row->gst ?? '' }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->amount }}</td>
                            <td>{{ $row->invoice_amount ?? '' }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>

    </div>
    <script>
        $('#exportToExcelTally').click(function() {
            var name = $(this).data("name");

            var table = document.getElementById('exportTable');

            var ws = XLSX.utils.table_to_sheet(table, {
                raw: true
            });

            // Loop through all cells
            Object.keys(ws).forEach(function(cell) {
                if (cell[0] === '!') return;

                let value = ws[cell].v;

                // match dd/mm/yyyy
                if (typeof value === 'string' && /^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                    ws[cell].t = 's'; // force STRING
                    ws[cell].z = '@'; // text format
                }
            });

            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Report");

            XLSX.writeFile(wb, name + '.xlsx');
        });
    </script>
@endsection
