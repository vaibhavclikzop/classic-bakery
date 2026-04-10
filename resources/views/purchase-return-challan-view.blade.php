@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Purchase Order View</title>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order View</h4>
            </div>
            <button onclick="printcontent()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print
            </button>
        </div>

        <div class="card-body" id="PrintOrder">


            <div class="text-center">
                <span><b></b></span>
            </div>

            <div class="text-center mt-2">
                <h4>{{ $setting->company_name }}</h4>
                <p>{{ $setting->address }}</p>
            </div>

            <div style="display:flex;border:1px solid;margin-top:5px">
                <div style="width:50%;border:1px solid;padding:5px">
                    GSTIN : {{ $setting->gst_no }} <br>
                    Email : {{ $setting->email }} <br>
                    Contact : {{ $setting->number }}
                </div>

                <div style="width:50%;border:1px solid;padding:5px">
                    Debit Note No : {{ $po_mst->debit_note_no }} <br>
                    Return Date : {{ $po_mst->return_date }} <br>
                    Invoice No : {{ $po_mst->invoice_no }} <br>
                </div>
            </div>


            <div style="display:flex;border:1px solid;margin-top:5px">
                <div style="width:50%;border:1px solid;padding:5px">
                    <b>Bill From</b><br>
                    Company: {{ $setting->company_name }}<br>
                    GSTIN : {{ $setting->gst_no }} <br>
                    Contact : {{ $setting->number }} <br>
                    Address: {{ $setting->address }} <br>
                </div>

                <div style="width:50%;border:1px solid;padding:5px">
                    <b>Vendor Details</b><br>
                    Vendor : {{ $po_mst->vendor }} <br>
                    Number: {{ $po_mst->number ?? 'NA' }}<br>
                    Address:{{ $po_mst->address }}, {{ $po_mst->city }}, {{ $po_mst->state }}
                </div>
            </div>

            <table class="w-100 mt-2">
                <thead>
                    <tr>
                        <th style="border:1px solid;font-size:11px">S.No</th>
                        <th style="border:1px solid;font-size:11px">Description of goods</th>
                        <th style="border:1px solid;font-size:11px">HSN Code</th>
                        <th style="border:1px solid;font-size:11px">UOM</th>

                        <th style="border:1px solid;font-size:11px">Qty</th>
                        <th style="border:1px solid;font-size:11px">Rate</th>
                        <th style="border:1px solid;font-size:11px">GST</th>
                        <th style="border:1px solid;font-size:11px">CESS</th>
                        <th style="border:1px solid;font-size:11px">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sno = 1;
                        $sub_total = 0;
                        $total_gst = 0;
                        $total_cess = 0;
                    @endphp

                    @foreach ($po_det as $item)
                        <tr>
                            <td style="border:1px solid;font-size:11px">{{ $sno++ }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->product }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->hsn_code }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->uom }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->qty }}</td>
                            <td style="border:1px solid;font-size:11px">{{ formatQtyPrice($item->price) }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->gst }}</td>
                            <td style="border:1px solid;font-size:11px">{{ $item->cess_tax }}</td>
                            <td style="border:1px solid;font-size:11px">{{ formatQtyPrice($item->total) }}</td>
                        </tr>

                        @php
                            $sub_total += $item->sub_total;
                            $total_gst += $item->gst_amount;
                            $total_cess += $item->cess_amount;
                        @endphp
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="7" style="border:1px solid"></th>
                        <th style="border:1px solid">Subtotal</th>
                        <th style="border:1px solid">{{ formatQtyPrice($sub_total) }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" style="border:1px solid"></th>
                        <th style="border:1px solid">GST</th>
                        <th style="border:1px solid">{{ formatQtyPrice($total_gst) }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" style="border:1px solid"></th>
                        <th style="border:1px solid">Cess</th>
                        <th style="border:1px solid">{{ formatQtyPrice($total_cess) }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" style="border:1px solid"></th>
                        <th style="border:1px solid">Delivery</th>
                        <th style="border:1px solid">{{ formatQtyPrice($stock_inward_mst->delivery_charges) }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" style="border:1px solid"></th>
                        <th style="border:1px solid">Grand Total</th>
                        <th style="border:1px solid">
                            {{ formatQtyPrice($sub_total + $total_gst + $total_cess + $stock_inward_mst->delivery_charges) }}
                        </th>
                    </tr>
                </tfoot>
            </table>


            <div class="d-flex justify-content-between mt-3">
                <div>
                    <b>Terms & Conditions</b>
                </div>

                <div>
                    <b>For {{ $setting->company_name }}</b><br><br>
                    Authorized Signatory
                </div>
            </div>

        </div>
    </div>
@endsection
