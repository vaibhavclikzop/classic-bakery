@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>
            Order View</title>
    @endpush
    <style>
        tr,
        td {
            border: solid 1px;
            padding: 5px;
        }
    </style>
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
            {{-- <div class="text-center">
                @if ($order_mst->status != 'pending')
                    <img src="/logo/{{ $setting->img }}" width="180px">
                @else
                    <h3> Order Challan</h3>
                @endif
            </div> --}}

            <table style="width: 100%; font-size: 11px; color: black; font-weight: bold">
                <tr>
                    <th style="border: solid 1px;padding: 5px; text-align: center" colspan="4">Manual Order</th>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;" style="border: solid 1px;padding: 5px;">Classic Bakery</td>
                    <td style="border: solid 1px;padding: 5px;">MO NO : MO-0326-14579</td>
                    <td style="border: solid 1px;padding: 5px;" colspan="2">Date : {{ $order_mst->delivery_date }} </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;">{!! $setting->address !!}</td>
                    <td style="border: solid 1px;padding: 5px;">Order ID : {{ $order_mst->order_id }}</td>
                    <td style="border: solid 1px;padding: 5px;" colspan="2"> Order Date : {{ $order_mst->delivery_date }}</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;">SO No. </td>
                    <td style="border: solid 1px;padding: 5px;">SO Date.</td>
                    <td style="border: solid 1px;padding: 5px;" colspan="2">FSSAI License NO :</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;"> GST : {{ $setting->gst_no }}</td>
                    <td style="border: solid 1px;padding: 5px;">PAN : </td>
                    <td style="border: solid 1px;padding: 5px;">State : CHANDIGARH</td>
                    <td style="border: solid 1px;padding: 5px;">State Code : 04</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;">Party Name : {{ $order_mst->customer_name }}
                        <br>
                        Address : {{ $order_mst->address }}<br>
                        @if ($order_mst && isset($order_mst->city))
                            {{ $order_mst->city }}, {{ $order_mst->state }}, {{ $order_mst->pincode }}<br>
                        @endif
                    </td>
                    <td style="border: solid 1px;padding: 5px;" colspan="3">

                    </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;">GSTIN : {{ $order_mst->gst }}</td>
                    <td style="border: solid 1px;padding: 5px;">CIN. </td>
                    <td style="border: solid 1px;padding: 5px;" colspan="2">Other References </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;">FSSAI NO.</td>
                    <td style="border: solid 1px;padding: 5px;" colspan="3">State @if ($order_mst && isset($order_mst->city))
                            {{ $order_mst->state }}
                        @endif
                    </td>
                </tr>
            </table>


            <div class="">


                @php
                    $sno = 1;
                @endphp
                <table class="" style="width: 100%;font-size: 11px; color: black; font-weight: bold">
                    <thead>
                        <th style="border: solid 1px;padding: 5px;">S.No</th>
                        @if ($order_mst->status != 'pending')
                            <th style="border: solid 1px;padding: 5px;">Sub Category</th>
                        @endif
                        <th style="border: solid 1px;padding: 5px;">Product</th>

                        <th style="border: solid 1px;padding: 5px;">Order Qty</th>
                        @if ($order_mst->status != 'pending')
                            <th style="border: solid 1px;padding: 5px;">Outward Qty</th>
                            <th style="border: solid 1px;padding: 5px;">Pending Qty</th>
                        @endif
                        <th style="border: solid 1px;padding: 5px;">Price</th>
                        <th style="border: solid 1px;padding: 5px;">Total</th>
                    </thead>
                    <tbody>
                        @php
                            $sub_total = 0;
                        @endphp
                        @foreach ($order_det as $item)
                            @php
                                $sub_total += $item->price * $item->qty;
                            @endphp
                            <tr>
                                <td style="border: solid 1px;padding: 5px;">{{ $sno++ }}</td>
                                @if ($order_mst->status != 'pending')
                                    <td style="border: solid 1px;padding: 5px;">{{ $item->sub_category }}</td>
                                @endif
                                <td style="border: solid 1px;padding: 5px;">{{ $item->product }}</td>
                                <td style="border: solid 1px;padding: 5px;">{{ formatQtyPrice($item->qty) }}</td>
                                @if ($order_mst->status != 'pending')
                                    <td style="border: solid 1px;padding: 5px;">{{ formatQtyPrice($item->booked_qty) }}</td>
                                    <td style="border: solid 1px;padding: 5px;">{{ $item->qty - $item->booked_qty }}</td>
                                @endif
                                <td style="border: solid 1px;padding: 5px;">{{ formatQtyPrice($item->price) }}</td>
                                <td style="border: solid 1px;padding: 5px;">{{ formatQtyPrice($item->price * $item->qty) }}</td>
                            </tr>
                        @endforeach


                  
               
                        <tr>
                            @php
                                $colspan = '6';
                            @endphp
                            @if ($order_mst->status == 'pending')
                                @php
                                    $colspan = '3';
                                @endphp
                            @endif
                            <th style="border: solid 1px;padding: 5px;" colspan={{ $colspan }}></th>
                            <th style="border: solid 1px;padding: 5px;">Sub Total</th>
                            <th style="border: solid 1px;padding: 5px;">{{ $sub_total }}</th>
                        </tr>
             </tbody>

                </table>
            </div>
            <div class="d-flex mt-4 justify-content-between">
                Declaration : <br>
                We Declare that this invoice shows the actual price of the goods described and that all particulars are true
                and correct.

            </div>
            <div style="text-align: center; margin-top: 10px">
                This is computer generated print and does not require signature
            </div>




        </div>

    </div>
@endsection
