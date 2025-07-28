@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Register</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Register</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">

                    </div>

                    <div>
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>
                </form>

            </div>
            <div>

                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body table-responsive" id="PrintOrder">
            <div class="page-title">
                <h4>Sale Register</h4>
            </div>
            <table class="w-100  ">
                <thead>
                    <tr style="border: solid 1px; padding:5px; background: #f0f0f0;">
                        <th style="border: solid 1px; padding:5px">S.No</th>
                        <th style="border: solid 1px; padding:5px">Customer</th>
                        <th style="border: solid 1px; padding:5px">Invoice Number</th>
                        <th style="border: solid 1px; padding:5px">Invoice Date</th>
                        <th style="border: solid 1px; padding:5px">Sub Total</th>
                        <th style="border: solid 1px; padding:5px">MRP</th>
                        <th style="border: solid 1px; padding:5px">Tax Amt.</th>
                        <th style="border: solid 1px; padding:5px">CESS</th>
                        <th style="border: solid 1px; padding:5px">CGST</th>
                        <th style="border: solid 1px; padding:5px">SGST</th>
                        <th style="border: solid 1px; padding:5px">IGST</th>
                        <th style="border: solid 1px; padding:5px">TCS Amt.</th>
                    </tr>
                </thead>
                <tbody id="sales-data">
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
                        if (!grouped[item.name]) grouped[item.name] = [];
                        grouped[item.name].push(item);
                    });

                    let html = '';
                    let snoStart = (page - 1) * perPage + 1;

                    Object.entries(grouped).forEach(([shopName, items]) => {
                        let subtotal = 0,
                            tax = 0,
                            cess = 0,
                            cgst = 0,
                            sgst = 0,
                            igst = 0,
                            dc_total = 0;
                        let sno = 1;
                        // Shop Header Row
                        html +=
                            `<tr><td colspan="12" style="font-weight:bold; background:#d1ecf1; border: solid 1px; padding:5px;">Shop : ${shopName}</td></tr>`;

                        items.forEach((item, index) => {
                            const itemTax = parseFloat(item.igst) + parseFloat(item.cgst) +
                                parseFloat(item.sgst) + parseFloat(item.cess_amt);
                            const dc = parseFloat(item.total_mrp);

                            subtotal += parseFloat(item.sub_total);
                            tax += itemTax;
                            cess += parseFloat(item.cess_amt);
                            cgst += parseFloat(item.cgst);
                            sgst += parseFloat(item.sgst);
                            igst += parseFloat(item.igst);
                            dc_total += dc;


                            html += `<tr style="border: solid 1px; padding:5px">
                <td style="border: solid 1px; padding:5px">${sno++}</td>
                <td style="border: solid 1px; padding:5px">${item.name}<br>${item.order_type}</td>
                <td style="border: solid 1px; padding:5px">${item.id}</td>
                <td style="border: solid 1px; padding:5px">${item.invoice_date}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.sub_total)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.total_mrp)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(itemTax)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.cess_amt)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.cgst)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.sgst)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.igst)}</td>
                <td style="border: solid 1px; padding:5px">0.00</td>
            </tr>`;
                        });

                        // Shop Total Row
                        html += `<tr style="background: #e2e3e5; font-weight: bold;">
            <td colspan="4" style="border: solid 1px; padding:5px;">Total</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(subtotal).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice( parseFloat(dc_total).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(tax).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(cess).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(cgst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(sgst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(igst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">0.00</td>
        </tr>`;
                    });

                    $('#sales-data').append(html);
                    page++;
                },
                complete: function() {

                    completeProgressBar();
                },
            });
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
