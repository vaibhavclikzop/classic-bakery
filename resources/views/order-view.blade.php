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
                        Delivery Date : {{ $setting->gst_no }} <br>
                        Description : {{ $order_mst->description }}

                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        <h6>Order ID : {{ $order_mst->order_id }}</h6>
                        <h4>{{ $order_mst->customer_name }}</h4>
                        <p>
                            {{ $order_mst->address }}<br>
                            @if ($order_mst && isset($order_mst->city))
                                {{ $order_mst->city }}, {{ $order_mst->state }}, {{ $order_mst->pincode }}<br>
                            @endif

                            {{ $order_mst->email }}<br>
                            {{ $order_mst->number }}<br>
                            {{ $order_mst->gst }}<br>
                            {{ $order_mst->delivery_date }}

                        </p>

                    </div>
                </div>
            </div>
            <div class="">
                <hr>
                <h6>Products</h6>
                @php
                    $sno = 1;
                @endphp
                <table class="table">
                    <thead>
                        <th>S.No</th>
                        <th>Sub Category</th>
                        <th>Product</th>
                        <th>Order Qty</th>
                        <th>Outward Qty</th>
                        <th>Pending Qty</th>

                        <th>Price</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @php
                            $sub_total=0;
                        @endphp
                        @foreach ($order_det as $item)
                        @php
                            $sub_total += $item->price * $item->qty;
                        @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->sub_category }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ formatQtyPrice($item->qty) }}</td>
                                <td>{{ formatQtyPrice($item->booked_qty) }}</td>
                                <td>{{ $item->qty - $item->booked_qty }}</td>
                                <td>{{ formatQtyPrice($item->price) }}</td>
                                <td>{{ formatQtyPrice($item->price * $item->qty) }}</td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6"></th>
                            <th >Sub Total</th>
                            <th>{{$sub_total}}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div class="d-flex mt-4 justify-content-between">
                <div>
                    <p><b><u><i>Terms & Conditions</i></u></b></p>
                    <ol style="list-style:number;">


                    </ol>
                </div>
                <div>
                    <h6 class="float-end">For {{ $setting->company_name }}</h6>

                    <p class="mt-5">Authorized Signatory</p>
                </div>

            </div>




        </div>

    </div>
@endsection
