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
                    <th style="border: solid 1px;padding: 5px;color:black; text-align: center" colspan="4">Manual Order
                    </th>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black"
                        style="border: solid 1px;padding: 5px;color:black">Classic Bakery</td>
                    <td style="border: solid 1px;padding: 5px;color:black">MO NO : </td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="2">Date :
                        {{ $order_mst->delivery_date }} </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black">{!! $setting->address !!}</td>
                    <td style="border: solid 1px;padding: 5px;color:black">Order ID : {{ $order_mst->order_id }}</td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="2"> Order Date :
                        {{ $order_mst->delivery_date }}</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black">SO No. </td>
                    <td style="border: solid 1px;padding: 5px;color:black">SO Date.</td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="2">FSSAI License NO :</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black"> GST : {{ $setting->gst_no }}</td>
                    <td style="border: solid 1px;padding: 5px;color:black">PAN : </td>
                    <td style="border: solid 1px;padding: 5px;color:black">State : CHANDIGARH</td>
                    <td style="border: solid 1px;padding: 5px;color:black">State Code : 04</td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black">Party Name : {{ $order_mst->customer_name }}
                        <br>
                        Address : {{ $order_mst->address }}<br>
                        @if ($order_mst && isset($order_mst->city))
                            {{ $order_mst->city }}, {{ $order_mst->state }}, {{ $order_mst->pincode }}<br>
                        @endif
                    </td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="3">

                    </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black">GSTIN : {{ $order_mst->gst }}</td>
                    <td style="border: solid 1px;padding: 5px;color:black">CIN. </td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="2">Other References </td>
                </tr>
                <tr>
                    <td style="border: solid 1px;padding: 5px;color:black">FSSAI NO.</td>
                    <td style="border: solid 1px;padding: 5px;color:black" colspan="3">State @if ($order_mst && isset($order_mst->city))
                            {{ $order_mst->state }}
                        @endif
                    </td>
                </tr>
            </table>


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
                            <th style="border: solid 1px;padding: 5px;color:black">SrN</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Product Code</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Description of Goods</th>
                            <th style="border: solid 1px;padding: 5px;color:black">HSN/SAC</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Qty</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Rate</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Taxable Amt</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Tax Rate %</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Cess (%)</th>
                            <th style="border: solid 1px;padding: 5px;color:black">CGST</th>
                            <th style="border: solid 1px;padding: 5px;color:black">SGST</th>
                            <th style="border: solid 1px;padding: 5px;color:black">IGST</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Cess Amt</th>
                            <th style="border: solid 1px;padding: 5px;color:black">Total Amt(RS)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $total = 0;
                            $total_taxable = 0;
                            $total_gst = 0;
                            $total_cess = 0;
                            $gross_total = 0;
                            $qty = 0;
                            $igst_amt = 0;
                            $cgst_amt = 0;
                            $sgst_amt = 0;
                            $total_mrp = 0;
                            $rate = 0;
                            $taxable = 0;
                            $gst = 0;
                            $cess = 0;
                            $total_qty = 0;
                            $cgst_total = 0;
                            $sgst_total = 0;
                            $igst_total = 0;
                            $grand_total = 0;
                        @endphp
                        @foreach ($order_det as $item)
                            @php

                                $qty = $item->qty;
                                $total_qty += $item->qty;
                                $tax_rate = $item->gst;
                                $rate = formatQtyPrice($item->price);
                                $taxable = formatQtyPrice(
                                    ($item->price * $item->qty) / (1 + $item->gst / 100) -
                                        (($item->price * $item->qty) / 100) * $item->cess_amt,
                                );
                                $gst = formatQtyPrice(
                                    $item->price * $item->qty - ($item->price * $item->qty) / (1 + $item->gst / 100),
                                );
                                $cess = formatQtyPrice((($item->price * $item->qty) / 100) * $item->cess_amt);

                                $total = $taxable + $gst + $cess;
                                $qty += $item->qty;
                                $total_taxable += $taxable;
                                $total_gst += $gst;
                                $total_cess += (($item->price * $item->qty) / 100) * $item->cess_amt;
                                $gross_total += $total;
                                $total_mrp += $item->mrp * $item->qty;
                                if ($item->gst_type == 'Outer GST') {
                                    $igst_amt = $gst;
                                    $igst_total += $gst;
                                } else {
                                    $cgst_amt = $gst / 2;
                                    $sgst_amt = $gst / 2;
                                    $cgst_total += $gst / 2;
                                    $sgst_total += $gst / 2;
                                }
                                $grand_total += $total;
                            @endphp

                            <tr>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ $sno++ }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ $item->article_no }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ $item->product }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ $item->hsn }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($item->qty, 2) }}
                                </td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($rate, 2) }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($taxable, 2) }}
                                </td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ $tax_rate }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">0</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($cgst_amt, 2) }}
                                </td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($sgst_amt, 2) }}
                                </td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($igst_amt, 2) }}
                                </td>
                                <td style="border: solid 1px;padding: 5px;color:black"> {{ $item->cess_amt }}</td>
                                <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($total, 2) }}</td>
                            </tr>
                        @endforeach


                        <tr>
                            <td colspan="4" align="right" style="border: solid 1px;padding: 5px;color:black"><b>Gross
                                    Total</b></td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($total_qty, 2) }}</td>
                            <td style="border: solid 1px;padding: 5px;color:black"></td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($total_taxable, 2) }}
                            </td>
                            <td style="border: solid 1px;padding: 5px;color:black"></td>
                            <td style="border: solid 1px;padding: 5px;color:black"></td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($cgst_total, 2) }}</td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($sgst_total, 2) }}</td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($igst_total, 2) }}</td>
                            <td style="border: solid 1px;padding: 5px;color:black">0</td>
                            <td style="border: solid 1px;padding: 5px;color:black">{{ number_format($grand_total, 2) }}
                            </td>
                        </tr>


                        <tr>
                            <td colspan="12" align="right" style="border: solid 1px;padding: 5px;color:black"><b>TCS (%
                                    of gross total)</b></td>
                            <td colspan="2" style="border: solid 1px;padding: 5px;color:black">0</td>
                        </tr>


                        <tr>
                            <td colspan="12" align="right" style="border: solid 1px;padding: 5px;color:black"><b>Grand
                                    Total</b></td>
                            <td colspan="2" style="border: solid 1px;padding: 5px;color:black">
                                <b>{{ number_format($grand_total, 2) }}</b>
                            </td>
                        </tr>

                    </tbody>

                </table>

                <br>

                {{-- <b>In Words : {{number_format($grand_total,2)}} Rupees</b> --}}

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
