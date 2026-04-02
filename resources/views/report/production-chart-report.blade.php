@extends('layouts.main')

@section('main-section')
    @push('title')
        <title>Production Chart Report</title>
    @endpush

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
                <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>

        </div>



        <div class="card-body" id="PrintOrder">

            <div class="text-center mb-3">

                <h4>Classic Bakery</h4>
                <h5>Production Chart Report</h5>

            </div>

            <div id="exportTable"></div>



            <div class="text-center mt-3" id="progress-container" style="display:none">

                <div class="progress">

                    <div class="progress-bar progress-bar-striped bg-info" id="progress-bar" style="width:0%">

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

                        html += `
    <h5 style="background:#d1ecf1;padding:8px;">
        Category : ${cat}
    </h5>

    <div class="row">
    `;

                        let subEntries = Object.entries(subs);
                        let half = Math.ceil(subEntries.length / 2);

                        let leftSubs = subEntries.slice(0, half);
                        let rightSubs = subEntries.slice(half);

                        // ✅ LEFT COLUMN
                        html += `<div class="col-md-6">`;

                        leftSubs.forEach(([sub, items]) => {

                            html += renderSubTable(sub, items);

                        });

                        html += `</div>`;

                        // ✅ RIGHT COLUMN
                        html += `<div class="col-md-6">`;

                        rightSubs.forEach(([sub, items]) => {

                            html += renderSubTable(sub, items);

                        });

                        html += `</div>`;

                        html += `</div>`;
                    });



                    $("#exportTable").append(html);

                    page++;

                },

                complete: function() {

                    completeProgressBar();

                }

            });

        }



        $("#load-more").click(fetchData);



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
    <div class="mb-3">

        <h6 style="background:#f0f0f0;padding:6px;">
            Sub Category : ${sub}
        </h6>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Order Qty</th>
                    <th>Actual Qty</th>
                </tr>
            </thead>

            <tbody>
                ${rows}

                <tr style="background:#fff3cd;font-weight:bold">
                    <td>Total</td>
                    <td>${subTotal.toFixed(0)}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

    </div>
    `;
}


        function completeProgressBar() {

            clearInterval(progressInterval);

            $("#progress-bar").css("width", "100%")
                .text("Completed");

            setTimeout(() => {

                $("#progress-container").fadeOut();

            }, 500);

        }
    </script>
@endsection
