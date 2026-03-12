@extends('layouts.main')

@section('main-section')

@push('title')
<title>Sale Register</title>
@endpush

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <div>
            <!-- <h4>Sale Register</h4> -->
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


    <div class="card-body" id="PrintOrder">

        <div class="text-center mb-3">

            <h4>Classic Bakery</h4>
            <h5>Itemwise Sales Report</h5>

        </div>

        <div id="exportTable"></div>


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

    function fetchSalesData() {

        $.ajax({

            url: "/getSaleRegisterReportData",

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

                const grouped = {};

                response.data.forEach((item) => {

                    if (!grouped[item.address]) {
                        grouped[item.address] = [];
                    }

                    grouped[item.address].push(item);

                });

                let html = '';

                Object.entries(grouped).forEach(([address, items]) => {

                    html += `

                        <div class="mb-4">

                        <h5 style="background:#d1ecf1;padding:8px;">
                        Address : ${address ?? 'Unknown'}
                        </h5>

                        <table class="table table-bordered table-sm w-100 saleTable">

                        <thead style="background:#f0f0f0">

                        <tr>

                        <th>Category Group</th>
                        <th>Category Name</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Value Sold</th>
                        <th>Qty After GVN</th>
                        <th>Value After GVN</th>

                        </tr>

                        </thead>

                        <tbody>

                        `;

                    items.forEach((item) => {

                        html += `

                        <tr>

                        <td>${item.category_group ?? ''}</td>

                        <td>${item.category_name ?? ''}</td>

                        <td>${item.item_name ?? ''}</td>

                        <td>${parseFloat(item.qty).toFixed(0)}</td>

                        <td>${parseFloat(item.value_sold).toFixed(2)}</td>

                        <td>${parseFloat(item.qty_after_gvn).toFixed(0)}</td>

                        <td>${parseFloat(item.value_after_gvn).toFixed(2)}</td>

                        </tr>

                        `;

                    });

                    html += `

                        </tbody>
                        </table>
                        </div>

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



    $('#load-more').on('click', fetchSalesData);

    $(document).ready(function() {

        fetchSalesData();

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