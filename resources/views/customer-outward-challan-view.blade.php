@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order View</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order View</h4>
            </div>
            <div class="">
                @if (empty($previousProduct->id))
                    <a class="btn btn-soft-success"> <i class="fa fa-backward" aria-hidden="true"></i> </a>
                @else
                    <a class="btn btn-success" href="/customer-outward-challan-view/{{ $previousProduct->id }}"> <i
                            class="fa fa-backward" aria-hidden="true"></i> </a>
                @endif

                @if (empty($nextProduct->id))
                    <a class="btn btn-soft-success"> <i class="fa fa-forward" aria-hidden="true"></i> </a>
                @else
                    <a class="btn btn-success" href="/customer-outward-challan-view/{{ $nextProduct->id }}"> <i class="fa fa-forward"
                            aria-hidden="true"></i> </a>
                @endif



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
                        <h6>Challan ID : {{ $order_mst->id }}</h6>
                        <h4>{{ $order_mst->customer_name }}</h4>
                        <p>


                            {{ $order_mst->number }} <br>
                            {{ $order_mst->invoice_date }}

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
                        <th>Category</th>
                        <th>Product</th>
                        <th>Article No</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>

                    </thead>
                    <tbody>
                        @php
                            $total=0;
                        @endphp
                        @foreach ($order_det as $item)
                        @php
                            $total += $item->price*$item->qty;
                        @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->sub_category }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->article_no }}</td>
                                <td>{{formatQtyPrice( $item->qty )}}</td>
                                <td>{{formatQtyPrice( $item->price )}}</td>
                                <td>{{formatQtyPrice( $item->price* $item->qty )}}</td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5"></th>
                            <th >Total</th>
                            <th>{{$total}}</th>
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
