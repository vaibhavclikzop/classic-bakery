@extends('layouts.main')

@section('main-section')

@push('title')
<title>Daily Advance Orders</title>
@endpush

<style>
    .print-wrapper {
        width: 100%;
        font-size: 12px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .top-info {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .order-box {
        border: 1px solid #999;
        padding: 8px;
        margin-top: 10px;
    }

    .dotted {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    .row-flex {
        display: flex;
        justify-content: space-between;
    }

    .left {
        width: 60%;
    }

    .right {
        width: 40%;
    }

    .bold {
        font-weight: bold;
    }

    .end-text {
        text-align: center;
        font-size: 11px;
    }

    @media print {

        @page {
            size: A4;
            margin: 5mm;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card,
        .card-body,
        .container,
        .container-fluid,
        .print-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        #PrintOrder {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .btn,
        .card-header {
            display: none !important;
        }

    }

    .header {
        display: none;
    }

    .card,
    .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    .print-wrapper {
        padding: 0 !important;
    }

    @page {
        size: A4;
        margin: 5mm;
    }
</style>

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h4>Daily Advance Orders</h4>

        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i> Print
        </button>

    </div>

    <div class="card-body" id="PrintOrder">

        <div class="print-wrapper">

            <div class="header">
                <h3>{{ $setting->company_name }}</h3>
                <div>Daily Advance Orders</div>
            </div>

            <div class="top-info">
                <div>Reporting For : {{ date('d/m/Y') }} - {{ date('d/m/Y') }}</div>
                <div>Printed On : {{ date('d-M-Y h:i:s A') }}</div>
            </div>

            <div class="dotted"></div>

            @php $sno=1; @endphp

            @foreach($data as $item)

            <div class="order-box">

                <div class="row-flex">

                    <div class="left">

                        <div>
                            <span class="bold">Sr No :</span> {{ $sno++ }}
                            <span class="bold"> {{ $item->name }}</span>
                        </div>

                        <div>
                            <span class="bold">Order Date :</span>
                            {{ date('d/m/Y',strtotime($item->order_date)) }}
                        </div>

                        @foreach($item->details as $i)

                        <div>
                            <span class="bold">Wt :</span> {{ $i->weight }}
                        </div>

                        <div>
                            {{ $i->product }} {{ $i->flavour }} - {{ $i->food_type }} {{ $i->shape }}
                        </div>

                        <div>
                            <span class="bold">Msg :</span> {{ $i->message }}
                        </div>

                        <div>
                            <span class="bold">Remark :</span> {{ $i->description }}
                        </div>

                        @endforeach

                    </div>


                    <div class="right">

                        <div>
                            <span class="bold">Order No :</span>
                            AO-{{ $item->id }}
                        </div>

                        <div>
                            <span class="bold">Delivery Date:</span>
                            {{ date('d/m/Y',strtotime($item->delivery_date)) }}
                        </div>

                           <div>
                            <span class="bold">Delivery Time:</span>
                            {{ date('h:i:A',strtotime($item->delivery_time)) }}
                        </div>
                        @foreach($item->details as $i)

                        <div>
                            <span class="bold">Qty :</span> {{ $i->qty }}
                        </div>

                        <div>
                            <span class="bold">Name :</span> {{ $i->name }}
                        </div>

                        @endforeach

                    </div>

                </div>

                <div class="dotted"></div>

                <div class="end-text">
                    END OF Daily Advance Orders
                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>

@endsection