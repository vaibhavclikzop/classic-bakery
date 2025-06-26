@extends('layouts.main')
@section('main-section')
    <div class="">
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget w-100">
                    <div class="dash-widgetimg">
                        <span><img src="images/dash1.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5> <span class="counters" data-count="{{ $total_delivered }}"> {{ $total_delivered }}</span></h5>
                        <h6>Total Delivered</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash1 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="images/dash2.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters" data-count="{{ $total_pending }}">{{ $total_pending }}</span></h5>
                        <h6>Total Pending </h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash2 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="images/dash3.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>₹ <span class="counters"
                                data-count="0">0</span>
                        </h5>
                        <h6>This Month Completed</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash3 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="images/dash4.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters" data-count="{{ $this_month_delivered }}"></span></h5>
                        <h6>This Month Delivered</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-count">
                    <div class="dash-counts">
                        <h4>{{ $customers }}</h4>
                        <h5><a href="/customers" class="text-white"> Customers</a></h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das1">
                    <div class="dash-counts">
                        <h4>{{ $vendor }}</h4>
                        <h5><a class="text-white" href="/vendor"> Vendor</a></h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das2">
                    <div class="dash-counts">
                        <h4>{{ $products }}</h4>
                        <h5><a href="/products" class="text-white"> Products</a></h5>
                    </div>
                    <div class="dash-imgs">
                        <img src="images/file-text-icon-01.svg" class="img-fluid" alt="icon">
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das3">
                    <div class="dash-counts">
                        <h4>{{ $minimum_stock }}</h4>
                        <h5><a href="near-by-minimum-stock" class="text-white"> Near by minimum stock</a></h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="file"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Button trigger modal -->

        <div class="row">
            <div class="col-xl-7 col-sm-12 col-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Complete/Delivered</h5>

                    </div>
                    <div class="card-body">
                        <div id="chart">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent Order</h4>
                        <div class="view-all-link">
                            <a href="javascript:void(0);" class="view-all d-flex align-items-center">
                                View All<span class="ps-2 d-flex align-items-center"><i data-feather="arrow-right"
                                        class="feather-16"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive dataview">
                            <table class="table dashboard-recent-products">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Delivery Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sno = 1;
                                    @endphp
                                    @foreach ($recent_order as $item)
                                        <tr>
                                            <td>{{ $sno++ }}</td>
                                            <td>{{ $item->customer }}</td>
                                            <td>{{ $item->delivery_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
 


        </div>

    </div>

    @php
        //   $month=implode(", ",$months);
        $month = json_encode($months);
        $completes = json_encode($completeResult);
        $delivered = json_encode($delivered_result);
     

    @endphp



    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script>
        var months = {!! $month !!};
        var completes = {!! $completes !!};
        var delivered = {!! $delivered !!};

        var numericValues = Object.values(completes).map(Number);
        var deliveredValues = Object.values(delivered).map(Number);
       
        var options = {
            series: [{
                name: 'Complete',
                type: 'column',
                data: numericValues
            }, {
                name: 'Delivered',
                type: 'column',
                data: deliveredValues
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: months,
            },

            tooltip: {
                fixed: {
                    enabled: true,
                    position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
                    offsetY: 30,
                    offsetX: 60
                },
            },
            legend: {
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endsection
