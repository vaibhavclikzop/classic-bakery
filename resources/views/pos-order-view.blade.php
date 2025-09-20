@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Invoice View</title>
    @endpush


    <div class="card">
        <div class="card-header">
            <button id="" onclick="printcontent()" type="button" class="btn btn-primary float-end">Print</button>
        </div>
        @if ($data)
            <div class="card-body" id="PrintOrder" style="display: flex;justify-content: center">
                <div style="width: 80mm; ">
                    <div style="text-align: center">
                        <h5>{{ $setting->company_name }}</h5>
                        <h6>{{ $setting->address }} </h6>
                        <p style="padding: 0; margin:0">FSSAI NO : {{ $setting->fssai_no }} <br>
                            GST NO : {{ $setting->gst_no }}</p>
                        <h5 style="margin-top: 0px; padding-top:0">TAX INVOICE</h5>


                    </div>
                    <div>
                        <span>Invoice No : {{ $data->invoice_no }}</span>
                        <span style="float: right">Dt : {{ $data->created_at }}</span>
                    </div>
                    <div>
                        <hr style="border:solid 1px">
                    </div>
                    <div>
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width: 50%; padding-right:80px">Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Amt.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->productDetails as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ formatQtyPrice($item->qty) }}</td>
                                        <td>{{ formatQtyPrice($item->price) }}</td>
                                        <td>{{ formatQtyPrice($item->price * $item->qty) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right">Sub Total : </td>
                                    <td>{{ $data->sub_total }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right">Discount : </td>
                                    <td>{{ $data->discount_percentage }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right">Extra Charges :
                                    </td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right">Round off : </td>
                                    <td>{{ ceil($data->total) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right">Bill Total :
                                    </td>
                                    <td>{{ ceil($data->total) }}</td>
                                </tr>
                            </tfoot>
                        </table> 
                    </div>
                    <div style="text-align: center">
                        <h4>!!! Thanks Visit Again !!!</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
