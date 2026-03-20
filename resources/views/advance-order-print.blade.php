@extends('layouts.main')

@section('main-section')

@push('title')
<title>Order View</title>
@endpush

<style>
    .order-box {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        page-break-inside: avoid;
        overflow: hidden;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .company-info h3 {
        margin: 0;
        font-weight: 700;
    }

    .company-info p {
        font-size: 13px;
        margin: 3px 0;
    }

    .order-details {
        text-align: right;
        font-size: 13px;
    }

    .product-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        word-wrap: break-word;
        margin-top: 15px;
    }

    .product-table th,
    .product-table td {
        border: 1px solid #ddd;
        padding: 5px;
        font-size: 11px;
        text-align: center;
    }

    .product-table th:first-child,
    .product-table td:first-child {
        width: 60px;
    }

    .cake-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    @media print {

        body * {
            visibility: hidden;
        }

        #PrintOrder,
        #PrintOrder * {
            visibility: visible;
        }

        #PrintOrder {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .order-box {
            page-break-inside: avoid;
        }

        .btn {
            display: none;
        }

    }

    @page {
        size: A4;
        margin: 10mm;
    }
</style>


<div class="card">

    <div class="card-header d-flex justify-content-between">

        <div class="page-title">
            <h4>Order View</h4>
        </div>

        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print
            </button>
        </div>

    </div>


    <div class="card-body" id="PrintOrder">

        @foreach ($data as $item)

        <div class="order-box">


            <div class="order-header">

                <div class="company-info">

                    <h3>{{ $setting->company_name }}</h3>

                    <p>
                        {!! $setting->address !!}<br>
                        Email : {{ $setting->email }} <br>
                        Phone : {{ $setting->number }} <br>
                        GST : {{ $setting->gst_no }}
                    </p>

                </div>

                <div class="order-details">

                    <h4>Order ID : #{{ $item->id }}</h4>

                    Shop : {{ $item->name }} <br>
                    Order Type : {{ $item->type }} <br>

                    Order Date : {{ $item->order_date }} <br>
                    Delivery Date : {{ $item->delivery_date }} <br>
                    Delivery Time : {{ $item->delivery_time }}

                </div>

            </div>

            <hr>

            <table class="product-table">

                <thead>

                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Flavour</th>
                        <th>Weight</th>
                        <th>Shape</th>
                        <th>Food Type</th>
                        <th>Name</th>
                        <th>Message</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach ($item->details as $i)

                    <tr>

                        <td>

                            @php
                            $images = explode(', ', $i->files);
                            @endphp

                            @if ($i->files)

                            @foreach ($images as $k)

                            <img src="/cake images/{{ $k }}" class="cake-img">

                            @endforeach

                            @endif

                        </td>

                        <td>{{ $i->product }}</td>
                        <td>{{ $i->flavour }}</td>
                        <td>{{ $i->weight }}</td>
                        <td>{{ $i->shape }}</td>
                        <td>{{ $i->food_type }}</td>
                        <td>{{ $i->name }}</td>
                        <td>{{ $i->message }}</td>
                        <td>{{ $i->description }}</td>
                        <td>{{ $i->qty }}</td>
                        <td>₹ {{ $i->total_price }}</td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @endforeach

    </div>

</div>

@endsection