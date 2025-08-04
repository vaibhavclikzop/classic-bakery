@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Inward Report View</h4>
            </div>
            <div>

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
                        GST : {{ $setting->gst_no }} <br>
                        Delivery Date : {{ $setting->gst_no }}

                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        <h6>Invoice : {{ $stock_inward_mst->invoice_no }}</h6>
                        <h4>{{ $stock_inward_mst->po_name }}</h4>
                        <h4>{{ $stock_inward_mst->vendor }}</h4>
                        <p>

                            {{ $stock_inward_mst->received_material_date }}<br>
                            {{ $stock_inward_mst->invoice_date }}<br>
                            PO Inward No : {{ $stock_inward_mst->id }}<br>


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

                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>

                        <th>GST</th>
                        <th>Cess</th>
                        <th>Total</th>

                    </thead>
                    <tbody>
                        @php
                            $total_gst = 0;
                            $sub_total = 0;
                            $total_cess = 0;
                        @endphp
                        @foreach ($stock_inward_det as $item)
                            @php
                                $total_gst += (($item->price * $item->qty) / 100) * $item->gst;
                                $total_cess += (($item->price * $item->qty) / 100) * $item->cess_tax;
                                $sub_total += $item->price * $item->qty;
                            @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->product_name }}</td>
                                <td>{{ formatQtyPrice($item->qty) }}</td>
                                <td>{{ formatQtyPrice($item->price) }}</td>

                                <td>{{ formatQtyPrice($item->gst) }}</td>
                                <td>{{ formatQtyPrice($item->cess_tax) }}</td>



                                <td>{{ $item->price * $item->qty + (($item->price * $item->qty) / 100) * $item->gst + (($item->price * $item->qty) / 100) * $item->cess_tax }}
                                </td>



                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>Subtotal</th>
                            <th>{{ $sub_total }}</th>
                        </tr>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>GST</th>
                            <th>{{ $total_gst }}</th>
                        </tr>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>Cess </th>
                            <th>{{ $total_cess }}</th>
                        </tr>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>Delivery Charges </th>
                            <th>{{ formatQtyPrice($stock_inward_mst->delivery_charges) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>Grand Total</th>
                            <th>{{ formatQtyPrice($total_gst + $sub_total + $total_cess + $stock_inward_mst->delivery_charges) }}
                            </th>
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
