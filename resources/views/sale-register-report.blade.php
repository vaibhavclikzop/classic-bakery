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
            <table class="table  ">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer</th>
                        <th>Invoice Number</th>
                        <th>Invoice Date</th>
                        <th>Sub Total</th>
                        <th>MRP</th>
                        <th>Tax Amt.</th>
                        <th>CESS</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>TCS Amt.</th>


                    </tr>
                </thead>
                <tbody id="sales-data">

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

                    let html = '';
                    let snoStart = (page - 1) * perPage + 1;

                    response.data.forEach((item, index) => {
                        html += `<tr>
                    <td>${snoStart + index}</td>
                    <td>${item.name}<br>${item.order_type}</td>
                    <td>${item.id}</td>
                    <td>${item.invoice_date}</td>
                    <td>${item.sub_total}</td>
                    <td>${item.total_mrp}</td>
                    <td>${parseFloat(item.igst) + parseFloat(item.cgst) + parseFloat(item.sgst) + parseFloat(item.cess_amt)}</td>
                    <td>${item.cess_amt}</td>
                    <td>${item.cgst}</td>
                    <td>${item.sgst}</td>
                    <td>${item.igst}</td>
                    <td>0</td>
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
