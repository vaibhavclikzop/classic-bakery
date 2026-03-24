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


    <!-- <div class="">


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
            </div> -->
    <div style="width:100%; font-size:12px;">

        @php
        $sno = 1;
        $total_qty = 0;
        $taxable_total = 0;
        $cgst_total = 0;
        $sgst_total = 0;
        $igst_total = 0;
        $grand_total = 0;
        @endphp

        <table style="width:100%; border-collapse:collapse;" border="1">

            <thead style="background:#e5e5e5;">
                <tr>
                    <th>SrN</th>
                    <th>Product Code</th>
                    <th>Description of Goods</th>
                    <th>HSN/SAC</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Taxable Amt</th>
                    <th>Tax Rate %</th>
                    <th>Cess (%)</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Cess Amt</th>
                    <th>Total Amt(RS)</th>
                </tr>
            </thead>

            <tbody>

                @foreach($order_det as $item)

                @php
                $qty = $item->qty;
                $rate = $item->price;
                $tax_rate = $item->gst;

                $taxable = $qty * $rate;

                $cgst = 0;
                $sgst = 0;
                $igst = 0;

                if(!empty($item->igst)){

                $igst = ($taxable * $tax_rate) / 100;

                }else{

                $cgst = ($taxable * $tax_rate / 2) / 100;
                $sgst = ($taxable * $tax_rate / 2) / 100;

                }

                $total = $taxable + $cgst + $sgst + $igst;

                $total_qty += $qty;
                $taxable_total += $taxable;
                $cgst_total += $cgst;
                $sgst_total += $sgst;
                $igst_total += $igst;
                $grand_total += $total;

                @endphp

                <tr>
                    <td>{{$sno++}}</td>
                    <td>{{ $item->article_no }}</td>
                    <td>{{$item->product}}</td>
                    <td>{{$item->hsn}}</td>
                    <td>{{number_format($qty,2)}}</td>
                    <td>{{number_format($rate,2)}}</td>
                    <td>{{number_format($taxable,2)}}</td>
                    <td>{{$tax_rate}}</td>
                    <td>0</td>
                    <td>{{number_format($cgst,2)}}</td>
                    <td>{{number_format($sgst,2)}}</td>
                    <td>{{number_format($igst,2)}}</td>
                    <td>{{$item->cess_amt}}</td>
                    <td>{{number_format($total,2)}}</td>
                </tr>

                @endforeach


                <tr>
                    <td colspan="4" align="right"><b>Gross Total</b></td>
                    <td>{{number_format($total_qty,2)}}</td>
                    <td></td>
                    <td>{{number_format($taxable_total,2)}}</td>
                    <td></td>
                    <td></td>
                    <td>{{number_format($cgst_total,2)}}</td>
                    <td>{{number_format($sgst_total,2)}}</td>
                    <td>{{number_format($igst_total,2)}}</td>
                    <td>0</td>
                    <td>{{number_format($grand_total,2)}}</td>
                </tr>


                <tr>
                    <td colspan="12" align="right"><b>TCS (% of gross total)</b></td>
                    <td colspan="2">0</td>
                </tr>


                <tr>
                    <td colspan="12" align="right"><b>Grand Total</b></td>
                    <td colspan="2"><b>{{number_format($grand_total,2)}}</b></td>
                </tr>

            </tbody>

        </table>

        <br>

        <b>In Words : {{number_format($grand_total,2)}} Rupees</b>

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