@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Print Barcode</title>
    @endpush
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
        <div class="card-body" id="PrintOrder">


            @foreach ($data as $product)
                @for ($i = 1; $i <= $product['qty']; $i++)
                    <div style="height: 2.1cm; width: 6.95cm; border:solid 1px; margin-top:10px">
                        <div style="display: flex; justify-content: space-between">
                            <div style="font-size: 11px; padding:5px">
                                <p>
                                    {{ $product['name'] }} <br>
                                    MRP : {{ $product['price'] ?? '' }} <br>
                                    Date Used By : {{ $product['expiry'] }} <br>
                                    Inclusive of all taxes.
                                </p>
                            </div>
                            <div style="padding:3px">
                                {!! DNS2D::getBarcodeHTML($product['bar_code'] ?? '', 'QRCODE', 3.5, 3.5) !!}
                            </div>
                        </div>
                    </div>
                @endfor
            @endforeach




        </div>

    </div>
@endsection
