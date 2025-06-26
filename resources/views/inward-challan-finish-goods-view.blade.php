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
                {{-- @if (request('edit') == 1)
                    <a class="btn btn-dark" href="?edit=0"><i class="fa fa-eye" aria-hidden="true"></i></a>
                @else
                    <a class="btn btn-dark" href="?edit=1"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                @endif --}}

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
                        PO ID : {{ $po_mst->po_id }}
                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        <h4>{{ $po_mst->vendorDetails->company_name }}</h4>
                        <p>
                            {{ $po_mst->vendorDetails->name }} <br>
                            {{ $po_mst->vendorDetails->address }},<br> {{ $po_mst->vendorDetails->state }},
                            {{ $po_mst->vendorDetails->city }}, ,
                            {{ $po_mst->vendorDetails->pincode }} <br>
                            {{ $po_mst->vendorDetails->number }} <br>
                            {{ $po_mst->vendorDetails->email }} <br>
                            {{ $po_mst->vendorDetails->gst }} <br>

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
                            $cess_total = 0;
                        @endphp
                        @foreach ($po_det as $item)
                            @php
                                $total_gst += (($item->price * $item->qty) / 100) * $item->gst;
                                $cess_total += (($item->price * $item->qty) / 100) * $item->cess_tax;
                                $sub_total += $item->price * $item->qty;
                            @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->productDetails->name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->price }}</td>

                                <td>{{ $item->gst }}</td>
                                <td>{{ $item->cess_tax }}</td>



                                <td>{{ $item->price * $item->qty + (($item->price * $item->qty) / 100) * $item->gst +( (($item->price * $item->qty) / 100) * $item->cess_tax)}}</td>



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
                            <th>CESS</th>
                            <th>{{ $cess_total }}</th>
                        </tr>
                        <tr>
                            <th colspan="5">

                            </th>
                            <th>Grand Total</th>
                            <th>{{ $total_gst + $sub_total+ $cess_total  }}</th>
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
