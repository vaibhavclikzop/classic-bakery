@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order View</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order View</h4>
            </div>
            <div class="">


                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>


            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="text-center">
                <img src="/logo/{{ $setting->img }}" width="180px">
            </div>

            <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                <div>
                    <h3>{{ $setting->company_name }}</h3>
                    <p>{!! $setting->address !!}
                        <br>
                        E-Mail : {{ $setting->email }} <br>
                        Phone : {{ $setting->number }} <br>
                        GST : {{ $setting->gst_no }}


                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        <h6>Order ID : {{ $order_mst->order_id }}</h6>
                        <h4>Shop : {{ $order_mst->name }}</h4>
                        <h6>Order Type : {{ $order_mst->type }}</h6>
                        <p>
                            Order Date : {{ $order_mst->order_date }} <br>
                            Delivery Date : {{ $order_mst->delivery_date }} <br>
                            Deliver Time : {{ $order_mst->delivery_time }} <br>


                        </p>


                    </div>
                </div>
            </div>
            <div class="m-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Image</th>
                            <th>Item</th>
                            <th>Flavour</th>
                            <th>Weight</th>
                            <th>Shape</th>
                            <th>Food Type</th>
                            <th>Name</th>
                            <th>Message</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($order_det as $item)
                            @php
                                $images = explode(', ', $item->files);
                            @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>
                                    @foreach ($images as $i)
                                        <a href="/cake images/{{ $i }}" target="_blank"> <img
                                                src="/cake images/{{ $i }}" width="60px" class="m-1"></a>
                                        <br>
                                    @endforeach
                                </td>

                                <td>{{ $item->product }}</td>
                                <td>{{ $item->flavour }}</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->shape }}</td>
                                <td>{{ $item->food_type }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->message }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->total_price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection
