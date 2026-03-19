@extends('layouts.main')

@section('main-section')

@push('title')
<title>Finished Goods Upload</title>
@endpush

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <div class="page-title">
            <h4>Finished Goods Upload</h4>
        </div>

        <div>
            <form method="GET" class="d-flex">

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

            </form>
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

        <div class="text-center mb-3">
            <h4>Classic Bakery</h4>
            <h5>Finished Goods Upload Report</h5>
        </div>

        <table class="table table-bordered table-sm w-100" id="rmTable">

            <thead style="background:#f0f0f0">

                <tr>
                    <th>Sr No</th>
                    <th>ProdUpload</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>SubCategory</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Department</th>
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

                    <td>FI-${item.upload_code}</td>

                    <td>${item.upload_date}</td>

                    <td>${item.category_name ?? ''}</td>

                    <td>${item.sub_category_name ?? ''}</td>

                    <td>${item.item_name ?? ''}</td>

                    <td>${item.uom ?? ''}</td>

                    <td>${Math.round(item.qty)}</td>

                    <td>FG PRODUCTION</td>

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