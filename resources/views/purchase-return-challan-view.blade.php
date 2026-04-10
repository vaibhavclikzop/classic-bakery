@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order View</h4>
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
                        <h4>{{ $po_mst->company }}</h4>
                        <p>
                            {{ $po_mst->vendor }} <br>
                            {{ $po_mst->address }}, {{ $po_mst->state }}, {{ $po_mst->city }}, ,
                            {{ $po_mst->pincode }} <br>
                            {{ $po_mst->number }} <br>
                            {{ $po_mst->email }} <br>
                            {{ $po_mst->gst }} <br>


                            {{ $po_mst->return_date }} <br>

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
                            $sub_total = 0;
                            $total_gst = 0;
                            $total_cess = 0;
                        @endphp

                        @foreach ($po_det as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->product }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ formatQtyPrice($item->price) }}</td>
                                <td>{{ $item->gst }}</td>
                                <td>{{ $item->cess_tax }}</td>
                                <td>{{ formatQtyPrice($item->total) }}</td>



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
