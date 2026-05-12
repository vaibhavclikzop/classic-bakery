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
                <button id="exportToExcelTally" data-name="purchase variation report"
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
            <table class="table dataTable" id="exportTablePurchaseVariation">
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
                            <td>{{date("d-m-Y",strtotime($item->invoice_date )) }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ formatQtyPrice($item->price) }}</td>
                            <td>{{ formatQtyPrice($item->qty) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
        <script>
        $('#exportToExcelTally').click(function() {
            var name = $(this).data("name");

            var table = document.getElementById('exportTablePurchaseVariation');

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
