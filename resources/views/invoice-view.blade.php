@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Invoice View</title>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Invoice View</h4>
            </div>
            <div class="">
                {{-- @if (empty($previousProduct->id))
                    <a class="btn btn-soft-success"> <i class="fa fa-backward" aria-hidden="true"></i> </a>
                @else
                    <a class="btn btn-success" href="/outward-challan-view/{{ $previousProduct->id }}"> <i
                            class="fa fa-backward" aria-hidden="true"></i> </a>
                @endif

                @if (empty($nextProduct->id))
                    <a class="btn btn-soft-success"> <i class="fa fa-forward" aria-hidden="true"></i> </a>
                @else
                    <a class="btn btn-success" href="/outward-challan-view/{{ $nextProduct->id }}"> <i class="fa fa-forward"
                            aria-hidden="true"></i> </a>
                @endif --}}



                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>


            </div>
        </div>
        <div class="card-body" id="PrintOrder">

            <div>
                <div class="text-center">
                    <span>Tax Invoice</span>

                    <span class="float-end"> ORIGINAL FOR BUYER</span>

                </div>
                <div class="text-center mt-3">
                    <h4>{{ $setting->company_name }}</h4>
                    <h4>{{ $setting->address }}</h4>
                </div>


            </div>
            <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                <div style="border: solid 1px">
                    <div>
                        <div class="text-center">
                            @if($setting->img)
                            <img src="/logo/{{ $setting->img }}" width="180px">
                            @endif
                        </div>
                    </div>

                </div>
                <div style="border: solid 1px; width: 30%; padding: 5px">
                    <div>
                        IRN : TEST
                    </div>
                    <div>
                        ACK No. <br>
                        ACK Dt.
                    </div>
                    <div>
                        Ewb No. <br>
                        Ewb Dt.
                    </div>

                </div>
                <div style="width: 30%;">
                    <div>

                    </div>

                </div>
            </div>
            <div style="display: flex; justify-content: space-between; border: solid 1px;margin-top:5px">
                <div style="padding: 5px; border:solid 1px; width: 50%">
                    GSTIN : {{ $setting->gst_no }} <br>
                    FSSAI NO : {{ $setting->fssai_no }} <br>
                    PAN No : {{ $setting->pan_no }} <br>
                    CIN No : {{ $setting->cin_no }}
                </div>
                <div style="padding: 5px; border:solid 1px;width: 50%">
                    Invoice No : {{ $order_mst->id }} <br>
                    Invoice Date : {{ $order_mst->invoice_date }} <br>
                    Email : {{ $setting->email }} <br>
                    Contact No : {{ $setting->number }}
                </div>
                <div style="padding: 5px; border:solid 1px;width: 50%">
                    Mode of Transport: {{ $order_mst->mot }} <br>
                    Vehicle No : {{ $order_mst->vehicle_no }} <br>
                    Supply Date : {{ $order_mst->invoice_date }} <br>
                    Place of Supply : CHANDIGARH
                </div>

            </div>
            <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                <div style="padding: 5px; border:solid 1px; width: 50%">
                    (Billed To) {{ $order_mst->customer_name }}
                    <p>
                        FSSAI No : {{ $order_mst->fssai_no }} <br>
                        GSTIN : {{ $order_mst->gst }} <br>
                        Contact : {{ $order_mst->number }} <br>
                        Address : {{ $order_mst->address }}, {{ $order_mst->city }}, {{ $order_mst->state }},
                        {{ $order_mst->pincode }} <br>
                    </p>
                </div>
                <div style="padding: 5px; border:solid 1px; width: 50%">
                    (Shipped To) {{ $order_mst->customer_name }}
                    <p>
                        FSSAI No : {{ $order_mst->ship_fssai_no }} <br>
                        GSTIN : {{ $order_mst->ship_gst }} <br>
                        Contact : {{ $order_mst->number }} <br>
                        Address : {{ $order_mst->ship_address }}, {{ $order_mst->ship_city }},
                        {{ $order_mst->ship_state }}, {{ $order_mst->ship_pincode }} <br>
                    </p>
                </div>
            </div>


            <div class="">



                <table class="w-100">
                    <thead>
                        <th style="border:  solid 1px; padding:2px">S.No</th>

                        <th style="border:  solid 1px; padding:2px">Description of goods</th>
                        <th style="border:  solid 1px; padding:2px">HSN Code</th>
                        <th style="border:  solid 1px; padding:2px">UOM</th>
                        <th style="border:  solid 1px; padding:2px">MRP</th>
                        <th style="border:  solid 1px; padding:2px">Qty</th>


                        <th style="border:  solid 1px; padding:2px">Rate</th>
                        <th style="border:  solid 1px; padding:2px">Taxable</th>
                        <th style="border:  solid 1px; padding:2px">GST (%)</th>
                        <th style="border:  solid 1px; padding:2px">GST</th>
                        <th style="border:  solid 1px; padding:2px">CESS %</th>
                        <th style="border:  solid 1px; padding:2px">CESS</th>
                        <th style="border:  solid 1px; padding:2px">Total</th>

                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                            $total = 0;
                            $total_taxable = 0;
                            $total_gst = 0;
                            $total_cess = 0;
                            $gross_total = 0;
                            $qty = 0;
                            $igst_amt = 0;
                            $cgst_amt = 0;
                            $sgst_amt = 0;
                            $total_mrp=0;
                        @endphp

                        @foreach ($order_det as $item)
                            @php

                                if ($item->gst_type == 'Outer GST') {
                                    $igst_amt += (($item->price * $item->qty) / 100) * $item->gst;
                                } else {
                                    $cgst_amt += ((($item->price * $item->qty) / 100) * $item->gst) / 2;
                                    $sgst_amt += ((($item->price * $item->qty) / 100) * $item->gst) / 2;
                                }

                                $total =
                                    $item->price * $item->qty +
                                    (($item->price * $item->qty) / 100) * $item->gst +
                                    (($item->price * $item->qty) / 100) * $item->cess_amt;
                                $qty += $item->qty;
                                $total_taxable += $item->price * $item->qty;
                                $total_gst += (($item->price * $item->qty) / 100) * $item->gst;
                                $total_cess += (($item->price * $item->qty) / 100) * $item->cess_amt;
                                $gross_total += $total;
                                $total_mrp += $item->mrp *$item->qty;
                            @endphp
                            <tr>
                                <td style="border:  solid 1px; padding:2px">{{ $sno++ }}</td>

                                <td style="border:  solid 1px; padding:2px">{{ $item->product }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ $item->hsn_code }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ $item->uom }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->mrp) }}</td>

                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->qty) }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->price) }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->price * $item->qty) }}</td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->gst) }} </td>
                                <td style="border:  solid 1px; padding:2px">
                                    {{ formatQtyPrice((($item->price * $item->qty) / 100) * $item->gst) }} </td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($item->cess_amt) }} </td>
                                <td style="border:  solid 1px; padding:2px">
                                    {{ formatQtyPrice((($item->price * $item->qty) / 100) * $item->cess_amt) }} </td>
                                <td style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total) }}</td>

                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="border:  solid 1px; padding:2px" colspan="5">Total</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($qty) }}</th>
                            <th style="border:  solid 1px; padding:2px"></th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_taxable) }}</th>
                            <th style="border:  solid 1px; padding:2px"></th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_gst) }}</th>
                            <th style="border:  solid 1px; padding:2px"></th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_cess) }}</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($gross_total) }}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div style="display: flex; justify-content: space-between;  ">
                <div style="padding: 5px; border:solid 1px; width: 50%">
                    <table class="w-100">
                        <tr>
                            <th style="border:  solid 1px; padding:2px" colspan="2">In Words : </th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px" colspan="2">Bank Details : </th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Bank Name </th>
                            <th style="border:  solid 1px; padding:2px">NA</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Branch Name </th>
                            <th style="border:  solid 1px; padding:2px">NA</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Bank Account Number </th>
                            <th style="border:  solid 1px; padding:2px">NA</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Bank Branch IFSC : </th>
                            <th style="border:  solid 1px; padding:2px">NA</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Subject To </th>
                            <th style="border:  solid 1px; padding:2px">NA</th>
                        </tr>
                    </table>
                </div>
                <div style="padding: 5px; border:solid 1px; width: 50%">
                    <table class="w-100" style="text-align: right">
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Total Amount Before Tax</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_taxable) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Add CGST</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($cgst_amt) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Add SGST</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($sgst_amt) }}</th>
                        </tr>
                        <tr> 
                            <th style="border:  solid 1px; padding:2px">Add IGST</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($igst_amt) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Tax Amt. GST</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_gst) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Tax Amt. Cess</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($total_cess) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Amount After Tax</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($gross_total) }}</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">TCS Charges</th>
                            <th style="border:  solid 1px; padding:2px">00</th>
                        </tr>
                        <tr>
                            <th style="border:  solid 1px; padding:2px">Gross Invoice Total</th>
                            <th style="border:  solid 1px; padding:2px">{{ formatQtyPrice($gross_total) }}</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between;  ">
                <div >
                    <h5>GST Summary</h5>
                </div>
                <div >
                    <h5>MRP Total :  {{formatQtyPrice($total_mrp)}},  Dealer Margin :  {{formatQtyPrice($total_mrp-$gross_total) }}</h5>
                </div>
            </div>
        </div>

    </div>
@endsection
