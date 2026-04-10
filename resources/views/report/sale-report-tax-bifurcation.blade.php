@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Register</title>
    @endpush
    <style>
        .myTable>tr,
        th,
        td {
            border: solid 1px;
            padding: 5px
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Report GST Bifurcation</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">

                    </div>

                    <div class="mx-2">
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>

                    <div>
                        <label for="">Customer Type</label>
                        <select name="customer_type" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select</option>
                            <option value="customer" {{ request('customer_type') == 'customer' ? 'selected' : '' }}>Customer
                            </option>
                            <option value="outlet" {{ request('customer_type') == 'outlet' ? 'selected' : '' }}>Outlet
                            </option>
                        </select>

                    </div>
                </form>

            </div>
            <div>
                <button id="exportToExcel" data-name="sale report gst bifurcation"
                    class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary btn-sm"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body table-responsive" id="PrintOrder">
            <div class="page-title">
                <h4>Sale Register</h4>
            </div>
            <table class="myTable " id="exportTable">

                <tr>
                    <th>S.No</th>
                    <th>Status</th>
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Shop Name</th>
                    @foreach ($gstRates as $gst)
                        <th>Taxable {{ $gst->gst }}%</th>
                        <th>GST {{ $gst->gst }}%</th>
                    @endforeach
                    <th>Total GST</th>
                    <th>Total Amount</th>
                </tr>


                <tbody>
                    <!-- JavaScript will populate rows grouped by Shop here -->
                </tbody>

            </table>
            <div class="text-center mt-3" id="progress-container" style="display: none;">
                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped bg-info" id="progress-bar" style="width: 0%">
                        Loading...
                    </div>
                </div>
            </div>


            <div class="text-center mt-3">
                <button id="load-more" class="btn btn-primary">Load More</button>
            </div>

        </div>
    </div>

    <script>
        let page = 1;
        const perPage = 100;
        let gstRates = @json($gstRates);


        function fetchSalesData() {
            $.ajax({
                url: "/getSaleReportGstBifurcation",
                type: "GET",
                data: {
                    page: page,
                    fromDt: "{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}",
                    toDt: "{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}",
                    customer_type: "{{ request('customer_type') ?? '' }}",
                },
                beforeSend: function() {

                    startProgressBar();
                },
                success: function(response) {
                    if (response.data.length === 0) {
                        $('#load-more').hide();
                        return;
                    }
                    let sno = 1;
                    var inv = 0;
                    var html = "";
                    let grandTotal = 0;



                    response.data.forEach(element => {
                        let status = `<span class="badge bg-success">Complete</span>`;
                        if (element.status == "cancel") {
                            element.total_gst = 0;
                            element.total_amount = 0;
                            status = `<span class="badge bg-danger">Cancel</span>`;
                        }

                        let row = `
        <tr>
            <td>${sno++}</td>
            <td>${status}</td>
            <td>${element.id}</td>
            <td>${element.invoice_date}</td>
            <td>${element.name}</td>
    `;

                        // ✅ GST LOOP INSIDE ELEMENT LOOP
                       /* gstRates.forEach(gst => {


                            let rate = parseInt(gst.gst);
                            if (element.status == "cancel") {
                                rate = 0;
                            }


                            let taxable = element['taxable_' + rate] ?? 0;
                            let gstAmt = element['gst_' + rate] ?? 0;

                            row += `<td>${formatNumber(taxable)}</td>`;
                            row += `<td>${formatNumber(gstAmt)}</td>`;
                        }); */
                        

                        
                        gstRates.forEach(gst => {

                            let rate = parseInt(gst.gst);

                            let taxable = element['taxable_' + rate] ?? 0;
                            let gstAmt = element['gst_' + rate] ?? 0;

                          
                            if (element.status == "cancel") {
                                taxable = 0;
                                gstAmt = 0;
                            }

                            row += `<td>${formatNumber(taxable)}</td>`;
                            row += `<td>${formatNumber(gstAmt)}</td>`;
                        });

                        // ✅ totals
                        row += `
        <td>${formatNumber(element.total_gst)}</td>
        <td>${formatNumber(element.total_amount)}</td>
    </tr>
    `;

                        html += row;
                        grandTotal += parseFloat(element.total_amount);
                    });
                    html += `<tr><td colspan="16">Total</td><td>${formatNumber(grandTotal)}</td></tr>`;
                    $('#exportTable').append(html);

                    page++;
                },
                complete: function() {

                    completeProgressBar();
                },
            });
        }

        function formatNumber(value) {
            return parseFloat(value || 0).toFixed(2);
        }
        $('#load-more').on('click', fetchSalesData);

        // Initial load
        $(document).ready(fetchSalesData);



        let progressInterval;

        function startProgressBar() {

            let width = 0;
            $('#progress-bar').css('width', '0%').text('Loading...');
            $('#progress-container').show();

            progressInterval = setInterval(() => {
                if (width < 90) {
                    width += 1;
                    $('#progress-bar').css('width', width + '%');
                    $('#progress-bar').text("Generating Report " + width + "%");
                }
            }, 50);
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
