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
                <button id="exportToExcel" data-name="sale register report"
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
            <table class="w-100  ">
                <thead>

                </thead>
                <tbody id="exportTable">
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
                    toDt: "{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}",
                    customer_type: "{{ request('customer_type') }}"
                },
                beforeSend: function() {

                    startProgressBar();
                },
                success: function(response) {
                    if (response.data.length === 0) {
                        $('#load-more').hide();
                        return;
                    }
                    var inv = 0;
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
                        var total = 0;
                        var grand_total = 0;
                        let sno = 1;
                        // Shop Header Row
                        html +=
                            `
                            
                            <tr><td colspan="13" style="font-weight:bold; background:#d1ecf1; border: solid 1px; padding:5px;">Shop : ${shopName}</td></tr>
                            
                              <tr style="border: solid 1px; padding:5px; background: #f0f0f0;">
                        <th style="border: solid 1px; padding:5px">S.No</th>
                        <th style="border: solid 1px; padding:5px">Status</th>
                        <th style="border: solid 1px; padding:5px">Order Type</th>
                        <th style="border: solid 1px; padding:5px">Invoice Number</th>
                        <th style="border: solid 1px; padding:5px">Invoice Date</th>
                        <th style="border: solid 1px; padding:5px">Sub Total</th>
                        <th style="border: solid 1px; padding:5px">MRP</th>
                        <th style="border: solid 1px; padding:5px">Tax Amt.</th>
                        <th style="border: solid 1px; padding:5px">CESS</th>
                        <th style="border: solid 1px; padding:5px">CGST</th>
                        <th style="border: solid 1px; padding:5px">SGST</th>
                        <th style="border: solid 1px; padding:5px">IGST</th>
                        <th style="border: solid 1px; padding:5px">Total</th>
                    
                    </tr>
                            `;

                        var challan = "challan";
                        items.forEach((item, index) => {

                            let itemTax = 0;
                            let dc = 0;

                            if (item.is_invoice == 1) {
                                challan = item.id
                            } else {
                                inv++;
                                challan = item.id
                                // challan = "challan " + inv
                            }
                            let status = `<span class="badge badge-success">Complete</span>`;
                            if (item.status == "cancel") {
                                item.total_mrp = 0;
                                itemTax = 0;
                                item.cess_amt = 0;
                                item.cgst = 0;
                                item.sgst = 0;
                                item.igst = 0;
                                total = 0;

                                item.sub_total = 0;
                                status = `<span class="badge badge-danger">Cancel</span>`;
                            } else {
                                itemTax = parseFloat(item.igst) + parseFloat(item.cgst) +
                                    parseFloat(item.sgst) + parseFloat(item.cess_amt);
                                dc = parseFloat(item.total_mrp);

                                subtotal += parseFloat(item.sub_total - item.igst - item.cgst -
                                    item
                                    .sgst);
                                tax += parseFloat(itemTax);
                                cess += parseFloat(item.cess_amt);
                                cgst += parseFloat(item.cgst);
                                sgst += parseFloat(item.sgst);
                                igst += parseFloat(item.igst);
                                dc_total += dc;
                                total = parseFloat(item.sub_total - item.igst - item.cgst - item
                                    .sgst) + parseFloat(itemTax);

                                grand_total += total;

                            }

                            html += `<tr style="border: solid 1px; padding:5px">
                <td style="border: solid 1px; padding:5px">${sno++}</td>
                <td style="border: solid 1px; padding:5px"> ${status}</td>
                <td style="border: solid 1px; padding:5px"> ${item.order_type}</td>
                <td style="border: solid 1px; padding:5px">${challan}</td>
              <td style="border: solid 1px; padding:5px">
    ${("0" + new Date(item.invoice_date).getDate()).slice(-2) + "-" +
      ("0" + (new Date(item.invoice_date).getMonth()+1)).slice(-2) + "-" +
      new Date(item.invoice_date).getFullYear()}
</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(parseFloat(item.sub_total-item.igst-item.cgst-item.sgst).toFixed(2))}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.total_mrp)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(itemTax)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.cess_amt)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.cgst)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.sgst)}</td>
                <td style="border: solid 1px; padding:5px">${formatQtyPrice(item.igst)}</td>
                <td style="border: solid 1px; padding:5px">${parseFloat(total).toFixed(2)}</td>
             
                </tr>`;
                        });

                        // Shop Total Row
                        html += `<tr style="background: #e2e3e5; font-weight: bold;">
            <td colspan="5" style="border: solid 1px; padding:5px;">Total</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(subtotal).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(dc_total).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(tax).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(cess).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(cgst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(sgst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(igst).toFixed(2))}</td>
            <td style="border: solid 1px; padding:5px;">${formatQtyPrice(parseFloat(grand_total).toFixed(2))}</td>
  
        </tr>`;
                    });

                    $('#exportTable').append(html);
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
