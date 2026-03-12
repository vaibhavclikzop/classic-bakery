@extends('layouts.main')

@section('main-section')

@push('title')
<title>RM Consumption Report</title>
@endpush

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <div class="page-title">
            <h4>RM Consumption Report</h4>
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
            <button id="exportToExcel" data-name="rm consumption report"
                class="btn btn-success float-end btn-sm mx-2">Export
                to Excel</button>
            <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                    aria-hidden="true"></i> Print</button>

        </div>

    </div>


    <div class="card-body table-responsive" id="PrintOrder">

        <div class="text-center mb-3">
            <h4>Classic Bakery</h4>
            <h5>RM Consumption Report</h5>
        </div>

        <table class="table table-bordered table-sm w-100" id="rmTable">

            <thead style="background:#f0f0f0">

                <tr>

                    <th>Sr No</th>
                    <th>MI Code</th>
                    <th>MI Date</th>
                    <th>Category</th>
                    <th>Item</th>
                    <th>UOM</th>
                    <th>MI Qty</th>
                    <th>Last Price</th>
                    <th>Amount</th>
                    <th>From Location</th>
                    <th>To Location</th>

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

            url: "/getRmConsumptionReportData",

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

                    <td>MI-${item.mi_code}</td>

                    <td>${item.mi_date}</td>

                    <td>${item.category_name ?? ''}</td>

                    <td>${item.item_name ?? ''}</td>

                    <td>${item.uom ?? ''}</td>

                     <td>${ Math.round(item.mi_qty) }</td>
                    <td>${parseFloat(item.last_price).toFixed(2)}</td>

                    <td>${parseFloat(item.amount).toFixed(2)}</td>

                    <td>${item.from_location}</td>

                    <td>${item.to_location}</td>

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
</script>


@endsection