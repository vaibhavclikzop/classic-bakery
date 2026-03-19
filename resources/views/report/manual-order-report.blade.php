@extends('layouts.main')

@section('main-section')

@push('title')

<title>Manual Order Report</title>
@endpush

<style>
    .manual-header table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .manual-header td {
        border: 1px solid #9b9999;
        padding: 5px;
    }

    .manual-header .title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    #rmTable th,
    #rmTable td {
        border: 1px solid #9b9999;
        padding: 5px;
        font-size: 12px;
    }
</style>

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <div class="page-title">
            <h4>Manual Order Report</h4>
        </div>

        <div>

            <!-- <form method="GET" class="d-flex">

                <div class="mx-2">
                    <label>From</label>
                    <input type="date" name="fromDt" class="form-control"
                        onchange="this.form.submit()"
                        value="{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
                </div>

                <div class="mx-2">
                    <label>To</label>
                    <input type="date" name="toDt" class="form-control"
                        onchange="this.form.submit()"
                        value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">
                </div>

            </form> -->

        </div>

        <div>

            <button id="exportToExcel"
                class="btn btn-success btn-sm mx-2">Export to Excel</button>

            <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm">
                <i class="fa fa-print"></i> Print
            </button>

        </div>

    </div>

    <div class="card-body table-responsive" id="PrintOrder">

        <div class="text-center mb-2">
            <h4>Classic Bakery</h4>
        </div>

        <div class="manual-header mb-3">

            <table>

                <tr>
                    <td colspan="4" class="title">Manual Order</td>
                    <td><b>MO NO :</b> MO-0326-13724</td>
                    <td><b>Date :</b> 03-03-2026</td>
                </tr>

                <tr>
                    <td colspan="2"><b>Party Name :</b> KISAN NEHRU GOVT MODEL SCHOOL SECTOR-52</td>
                    <td><b>PO NO :</b></td>
                    <td><b>PO Date :</b> 04-03-2026</td>
                    <td><b>FSSAI License No :</b> 0000000000</td>
                    <td><b>State Code :</b> 04</td>
                </tr>

                <tr>
                    <td colspan="3">
                        <b>Party Name :</b> SHIKHA VERKA BOOTH 834<br>
                        Address : VERKA BOOTH 834 SECTOR 68 MOHALI
                    </td>

                    <td><b>State :</b> CHANDIGARH</td>
                    <td><b>GSTIN :</b></td>
                    <td><b>CIN :</b></td>
                </tr>

                <tr>
                    <td><b>FSSAI NO :</b></td>
                    <td><b>State :</b> PUNJAB</td>
                    <td colspan="4"><b>Other References :</b></td>
                </tr>

            </table>

        </div>

        <table class="table table-bordered table-sm w-100" id="rmTable">

            <thead style="background:#f0f0f0">

                <tr>
                    <th>Sr No</th>
                    <th>Product Code</th>
                    <th>Description of Goods</th>
                    <th>HSN/SAC</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Taxable Amt</th>
                    <th>Tax Rate</th>
                    <th>Cess (%)</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Cess Amt</th>
                    <th>Total Amt(RS)</th>
                </tr>

            </thead>

            <tbody id="exportTable"></tbody>

        </table>

        <div class="text-center mt-3" id="progress-container" style="display:none">

            <div class="progress">
                <div class="progress-bar progress-bar-striped bg-info"
                    id="progress-bar"
                    style="width:0%">
                    Loading...
                </div>
            </div>

        </div>

        <div class="text-center mt-3">
            <button id="load-more" class="btn btn-primary">
                Load More
            </button>
        </div>

    </div>

</div>

<script>
    let page = 1;
    const perPage = 100;

    function fetchRMData() {

        $.ajax({

            url: "/getFaStockReportData",

            type: "GET",

            data: {

                page: page,

                fromDt: "{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}",

                toDt: "{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}"

            },

            beforeSend: function() {
                startProgressBar();
            },

            success: function(response) {

                if (response.data.length === 0) {
                    $('#load-more').hide();
                    return;
                }

                let html = '';
                let sno = (page - 1) * perPage + 1;

                response.data.forEach((item) => {

                    html += `
<tr>

<td>${sno++}</td>

<td>${item.product_code ?? ''}</td>

<td>${item.item_name ?? ''}</td>

<td>${item.hsn ?? ''}</td>

<td>${item.qty ?? ''}</td>

<td>${item.rate ?? ''}</td>

<td>${item.taxable_amt ?? ''}</td>

<td>${item.tax_rate ?? ''}</td>

<td>${item.cess ?? ''}</td>

<td>${item.cgst ?? ''}</td>

<td>${item.sgst ?? ''}</td>

<td>${item.igst ?? ''}</td>

<td>${item.cess_amt ?? ''}</td>

<td>${item.total_amt ?? ''}</td>

</tr>
`;

                });

                $('#exportTable').append(html);

                page++;

            },

            complete: function() {
                completeProgressBar();
            }

        });

    }

    $('#load-more').on('click', fetchRMData);

    $(document).ready(function() {
        fetchRMData();
    });

    let progressInterval;

    function startProgressBar() {

        let width = 0;

        $('#progress-bar').css('width', '0%');

        $('#progress-container').show();

        progressInterval = setInterval(() => {

            if (width < 90) {
                width++;
                $('#progress-bar').css('width', width + '%');
                $('#progress-bar').text("Generating Report " + width + "%");
            }

        }, 40);

    }

    function completeProgressBar() {

        clearInterval(progressInterval);

        $('#progress-bar').css('width', '100%').text('Completed');

        setTimeout(() => {
            $('#progress-container').fadeOut();
        }, 500);

    }

    function printcontent() {

        var restorepage = document.body.innerHTML;

        var printcontent = document.getElementById('PrintOrder').innerHTML;

        document.body.innerHTML = printcontent;

        window.print();

        document.body.innerHTML = restorepage;

    }
</script>

@endsection