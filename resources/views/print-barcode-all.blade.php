@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Print Barcode</title>
    @endpush
    <style>
        @media print {
            div {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            p {
                font-size: 10px !important;
                margin: 0 !important;
                line-height: 1.1 !important;
            }
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Print Barcode</h4>
            </div>

            <div>
                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
            </div>

        </div>
        <div class="card-body" id="PrintOrder" style="color: black">


            @foreach ($data as $product)
                @for ($i = 1; $i <= $product['qty']; $i++)
                    <div
                        style="height: 2.1cm; width: 6.95cm; border:1px solid #000; margin-top:10px;
                    display:flex; justify-content:space-between; align-items:flex-start;
                    padding:3px; box-sizing:border-box;">
                        <div style="flex:1; font-size: 11px; line-height: 1.2; padding-right:5px; overflow:hidden;">
                            <p style="margin:0; font-weight:bold;">
                                {{ $product['name'] }}
                            </p>
                            <p style="margin:0; color: black; font-weight: 900">
                                MRP : {{ $product['price'] ?? '' }}
                            </p>
                            @if ($product['f_category_id'] == 1)
                                <p style="margin:0; color: black; font-weight: 900">
                                    Date Used By : {{ date('d-m-Y', strtotime($product['expiry'])) }}

                                </p>
                            @endif
                            <p style="margin:0; color: black; font-weight: 900">
                                Inclusive of all taxes.
                            </p>
                        </div>


                        <div style="width:2cm; height:2cm; flex-shrink:0; text-align:center;padding-bottom:3px;">
                            <img src="data:image/png;base64,{!! DNS2D::getBarcodePNG($product['bar_code'] ?? '', 'QRCODE', 7, 7) !!}" alt="QR Code"
                                style="width:100%; height:100%; object-fit:contain;">
                        </div>
                    </div>
                @endfor
            @endforeach




        </div>

    </div>
@endsection
