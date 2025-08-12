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


            @php
                $qty = request('qty')[0] ?? 1

            @endphp
            @for ($i = $qty; $i >= 1; $i--)
                <div style="height: 2.1cm; width: 6.95cm; border:solid 1px; margin-top:10px">
                    <div style="display: flex; justify-content: space-between">
                        <div style="font-size: 11px; padding:5px">
                            <p>
                                {{ $data->name }} <br>
                                MRP : {{ $data->price }} <br>
                                Date Used By : {{ request("expiry") }} <br>

                                Inclusive of all taxes.
                            </p>
                        </div>
                        <div style="padding:3px">
                            {!! DNS2D::getBarcodeHTML($data->bar_code, 'QRCODE', 3.5, 3.5) !!}
                        </div>

                    </div>
                </div>
            @endfor



        </div>

    </div>
@endsection
