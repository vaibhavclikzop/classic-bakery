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


                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>


            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            {{-- <div class="text-center">
                <img src="/logo/{{ $setting->img }}" width="180px">
            </div> --}}
            @foreach ($data as $item)
                <div style=" border: solid 1px; margin-top: 10px; height: 135mm;">


                    <div style="display: flex; justify-content: space-between; padding: 8px;">
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
                                <h6>Order ID : {{ $item->id }}</h6>
                                <h4>Shop : {{ $item->name }}</h4>
                                <h6>Order Type : {{ $item->type }}</h6>
                                <p>
                                    Order Date : {{ $item->order_date }} <br>
                                    Delivery Date : {{ $item->delivery_date }} <br>
                                    Deliver Time : {{ $item->delivery_time }} <br>


                                </p>


                            </div>
                        </div>
                    </div>

                    <div class="m-3">
                        @foreach ($item->details as $i)
                            <div class="d-flex justify-content-around">
                                <div class=" ">
                                    @php

                                        $images = explode(', ', $i->files);
                                    @endphp
                                    @if ($i->files)
                                        @foreach ($images as $k)
                                            <a href="/cake images/{{ $k }}" target="_blank" class="mt-2"> <img
                                                    src="/cake images/{{ $k }}" width="190px"
                                                    class="m-1"></a>
                                        @endforeach
                                    @endif
                                </div>
                                <div class=" ">

                                    Item : {{ $i->product }}<br>
                                    Flavour : {{ $i->flavour }}<br>
                                    Weight : {{ $i->weight }}<br>
                                    Shape : {{ $i->shape }}<br>
                                    Food Type : {{ $i->food_type }}<br>
                                    Name : {{ $i->name }}<br>
                                    Message : {{ $i->message }}<br>
                                    Description : {{ $i->description }}<br>
                                    Qty : {{ $i->qty }}<br>
                                    Price : {{ $i->total_price }}<br>
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>
            @endforeach



        </div>

    </div>
@endsection
