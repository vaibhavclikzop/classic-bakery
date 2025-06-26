@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Recipe View </h4>


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
                        GST : {{ $setting->gst_no }} <br>

                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        WO NO : {{ $data->wo_no }} <br>
                        Create at : {{ $data->created_at }}
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <table class="table">
                    <tr>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Article No</th>
                        <th>HSN Code</th>
                    </tr>
                    <tr>
                        <th>{{ $data->category }}</th>
                        <th>{{ $data->name }}</th>
                        <th>{{ $data->qty }}</th>
                        <th>{{ $data->article_no }}</th>
                        <th>{{ $data->hsn_code }}</th>
                    </tr>
                </table>
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
                        <th>UOM</th>


                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                            $total=0;

                        @endphp
                        @foreach ($det as $item)
                        @php
                            $total += $item->qty*$data->qty;
                        @endphp
                        <tr>
                            <td>{{$sno++}}</td>
                            <td>{{$item->product}}</td>
                            <td>{{$item->qty*$data->qty}}</td>
                            <td>{{$item->uom}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="2">Total</th>
                            <th colspan="2">{{$total}}</th>
                        </tr>
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
