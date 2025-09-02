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

                    </thead>
                    <tbody>
                        @php
                            $total_gst = 0;
                            $sub_total = 0;
                        @endphp
                        @foreach ($po_det as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->product }}</td>
                                <td>{{ $item->qty }}</td>



                            </tr>
                        @endforeach

                    </tbody>


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
