@extends('layouts.main')

@section('main-section')
    @push('title')
        <title>Production Chart Report</title>
    @endpush
    <style>
               /* 🔥 HEADER FULL WIDTH */
            .header-print {
                text-align: center;
                width: 100%;
                margin-bottom: 0px;
                break-after: avoid;
            }

            /* 🔥 APPLY COLUMN ONLY HERE */
            #print-content {
                column-count: 2;
                column-gap: 10px;
            }

            /* 🔥 PREVENT BREAKING */
            .sub-block {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 10px;
            }

            h5 {
                break-after: avoid;
            }

            table {
                margin-top: 12px;
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            table, th, td {
                border: 1px solid #000;
            }

            th, td {
                padding: 2px;
            }
    </style>

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <div>
                <h4>Production Chart Report</h4>
            </div>

            <div class="d-flex">

                <div class="mx-2">
                    <label>Date</label>
                    <input type="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="mx-2">

                    <label>Category</label>

                    <select id="category_id" class="form-control">

                        <option value="">All Category</option>

                        @foreach ($category as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach

                    </select>

                </div>
                <div class="mx-2">

                    <label>Customer Type</label>

                    <select id="customer_type" class="form-control">

                        <option value="">Select</option>

                        <option value="outlet">Outlet</option>
                        <option value="customer">Customer</option>

                    </select>

                </div>

                <div class="mx-2" style="width: 150px">

                    <label>Order Type</label>

                    <select id="order_type" class="form-control">

                        <option value="">Select</option>

                        @foreach ($order_type as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

            </div>

            <div>
                <button id="exportToExcel" data-name="rm consumption report"
                    class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>
                <button type="button" onclick="printReport()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>

        </div>



        <div class="card-body" id="PrintOrder">

            <div class="text-center mb-1 header-print">
                <p>Classic Bakery : Production Chart Report</h5>
            </div>
            <div id="print-content">
                <div id="exportTable"></div>
                <div <div class="text-center mt-3" id="progress-container" style="display:none">

                    <div class="progress">

                        <div class="progress-bar progress-bar-striped bg-info" id="progress-bar" style="width:0%">

                            Loading...

                        </div>

                    </div>

                </div>



                {{-- <div class="text-center mt-3">

                <button id="load-more" class="btn btn-primary">

                    Load More

                </button>

            </div> --}}

            </div>

        </div>



        <script>
            let page = 1;


            $("#load-more").click(fetchData);

            function fetchData() {

                $.ajax({

                    url: "/productionChartReportData",
                    type: "GET",

                    data: {
                        page: page,
                        date: $("#date").val(),
                        category_id: $("#category_id").val(),
                        customer_type: $("#customer_type").val(),
                        order_type: $("#order_type").val(),
                    },

                    beforeSend: function() {
                        startProgressBar();
                    },

                    success: function(response) {

                        if (response.data.length === 0) {
                            $("#load-more").hide();
                            return;
                        }

                        const grouped = {};

                        response.data.forEach(item => {

                            if (!grouped[item.category]) {
                                grouped[item.category] = {};
                            }

                            if (!grouped[item.category][item.sub_category]) {
                                grouped[item.category][item.sub_category] = [];
                            }

                            grouped[item.category][item.sub_category].push(item);

                        });

                        let html = "";

                        Object.entries(grouped).forEach(([cat, subs]) => {

                            // 🔥 SORT SUBCATEGORY ASC
                            let sortedSubs = Object.entries(subs)
                                .sort((a, b) => a[0].localeCompare(b[0]));

                            // html += `
                    //     <h5 style="background:#d1ecf1;padding:8px;">
                    //         Category : ${cat}
                    //     </h5>
                    // `;

                            // 🔥 SINGLE COLUMN FLOW
                            sortedSubs.forEach(([sub, items]) => {
                                html += renderSubTable(sub, items);
                            });

                        });

                        $("#exportTable").append(html);

                        page++;

                    },

                    complete: function() {
                        completeProgressBar();
                    }

                });
            }



            function renderSubTable(sub, items) {

                let subTotal = 0;
                let rows = '';

                items.forEach(i => {
                    let qty = parseFloat(i.qty);
                    subTotal += qty;

                    rows += `
        <tr>
            <td>${i.product}</td>
            <td>${qty.toFixed(0)}</td>
            <td></td>
        </tr>`;
                });

                return `
    <div class="sub-block">

     

        <table class="table table-bordered table-sm mt-3">
            <thead>
                <tr>
                    <th colspan="3" style="background-color:#D1ECF1">Sub Category : ${sub}</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Order Qty</th>
                    <th>Actual Qty</th>
                </tr>
            </thead>

            <tbody>
                ${rows}

                <tr class="total-row">
                    <td>Total</td>
                    <td>${subTotal.toFixed(0)}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

    </div>
    `;
            }



            $("#date,#category_id,#customer_type, #order_type").on("change", function() {

                page = 1;

                $("#exportTable").html("");

                $("#load-more").show();

                fetchData();

            });



            $(document).ready(function() {

                fetchData();

            });


            let progressInterval;

            function startProgressBar() {

                let width = 0;

                $("#progress-bar").css("width", "0%");

                $("#progress-container").show();

                progressInterval = setInterval(() => {

                    if (width < 90) {

                        width++;

                        $("#progress-bar").css("width", width + "%");

                        $("#progress-bar").text("Generating " + width + "%");

                    }

                }, 40);

            }

            function completeProgressBar() {

                clearInterval(progressInterval);

                $("#progress-bar").css("width", "100%")
                    .text("Completed");

                setTimeout(() => {

                    $("#progress-container").fadeOut();

                }, 500);

            }

            function printReport() {

                $(".buttons").hide();

                const content = document.getElementById('PrintOrder').innerHTML;

                const frame = document.createElement('iframe');
                frame.style.position = 'absolute';
                frame.style.top = '-10000px';
                document.body.appendChild(frame);

                const doc = frame.contentWindow.document;

                doc.open();
                doc.write(`
    <html>
    <head>
        <title>Print</title>
        <style>

@page {
    margin: 5mm;
}
            /* 🔥 HEADER FULL WIDTH */
            .header-print {
                text-align: center;
                width: 100%;
                margin-bottom: 0px;
                break-after: avoid;
            }

            /* 🔥 APPLY COLUMN ONLY HERE */
            #print-content {
                column-count: 2;
                column-gap: 10px;
            }

            /* 🔥 PREVENT BREAKING */
            .sub-block {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 10px;
            }

            h5 {
                break-after: avoid;
            }

            table {
                margin-top: 12px;
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
                    font-family: Calibri, Arial, sans-serif;
            }

            table, th, td {
                border: 1px solid #000;
            }

            th, td {
                padding: 2px;
            }

        </style>
    </head>
    <body>

        ${content}   <!-- 🔥 DIRECTLY USE CONTENT -->

    </body>
    </html>
    `);

                doc.close();

                setTimeout(() => {
                    frame.contentWindow.print();
                    document.body.removeChild(frame);
                }, 500);
            }
        </script>
    @endsection
