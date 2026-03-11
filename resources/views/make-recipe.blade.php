@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Recipe View </h4>
                <form action="">
                    <div class="input-group has-validation">

                        <input type="text" class="form-control" name="qty" value="{{ request('qty') ?? 1 }}" required>
                        <button class="input-group-text btn btn-dark"> Generate </button>
                    </div>
                </form>

            </div>
            <div>
                <form action="">
                    <select name="lang" id="" class="form-control" onchange="this.form.submit()">
                        <option value="English" {{ request('lang') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Hindi" {{ request('lang') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                    </select>
                </form>
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
                <table class="w-100">
                    <tr>
                        <th style="border: solid 1px; padding:0px 4px">{{ __('messages.name') }}</th>
                        <th style="border: solid 1px; padding:0px 4px">{{ __('messages.department') }}</th>

                        <th style="border: solid 1px; padding:0px 4px">{{ __('messages.no_of_batch') }}</th>
                        <th style="border: solid 1px; padding:0px 4px">{{ __('messages.per_unit') }}</th>
                        <th style="border: solid 1px; padding:0px 4px">Total</th>

                    </tr>

                    <tr>

                        <th style="border: solid 1px; padding:0px 4px ">{{ $data->name }}</th>
                        <th style="border: solid 1px; padding:0px 4px">{{ $data->dname }}</th>

                        <th style="border: solid 1px; padding:0px 4px">{{ $data->batch }}</th>
                        <th style="border: solid 1px; padding:0px 4px">{{ request('qty', 1) }}</th>
                        <th style="border: solid 1px; padding:0px 4px">{{ $data->batch * (float) request('qty', 1) }}</th>


                    </tr>
                </table>
            </div>
            <div class="">
                <hr>
                <h6>Products</h6>
                @php
                    $sno = 1;
                @endphp
                <table class="w-100">
                    <thead>
                        <th style="border: solid 1px; padding:0px 4px">S.No</th>
                        <th style="border: solid 1px; padding:0px 4px">Category</th>
                        <th style="border: solid 1px; padding:0px 4px">Product</th>
                        <th style="border: solid 1px; padding:0px 4px">Qty</th>
                        <th style="border: solid 1px; padding:0px 4px">UOM</th>
                        <th style="border: solid 1px; padding:0px 4px">Price</th>
                        <th style="border: solid 1px; padding:0px 4px">Total</th>


                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                            $total = 0;
                            $total_price = 0;

                        @endphp
                        @foreach ($det as $item)
                            @php
                                $total += $item->qty * request('qty', 1);
                                $total_price += $item->price * $item->qty * request('qty', 1);
                            @endphp
                            <tr>
                                <td style="border: solid 1px; padding:0px 4px">{{ $sno++ }}</td>
                                <td style="border: solid 1px; padding:0px 4px">{{ $item->category }}</td>
                                <td style="border: solid 1px; padding:0px 4px">
                                    {{ request('lang') == 'Hindi' ? $item->hindi : $item->product }}
                                </td>

                                <td style="border: solid 1px; padding:0px 4px">
                                    {{ formatQtyPrice($item->qty * request('qty', 1)) }}</td>
                                <td style="border: solid 1px; padding:0px 4px">{{ $item->uom }}</td>
                                <td style="border: solid 1px; padding:0px 4px">{{ formatQtyPrice($item->price) }}</td>
                                <td style="border: solid 1px; padding:0px 4px">
                                    {{ formatQtyPrice($item->price * $item->qty * request('qty', 1)) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="border: solid 1px; padding:0px 4px">Total</th>
                            <th colspan="3" style="border: solid 1px; padding:0px 4px">{{ formatQtyPrice($total) }}</th>
                            <th colspan="" style="border: solid 1px; padding:0px 4px">
                                {{ formatQtyPrice($total_price) }}</th>
                        </tr>
                    </tbody>


                </table>
            </div>
            <div class="d-flex mt-4 justify-content-between">
                <div style="padding: 20px">
                    <p><b><u><i>Description</i></u></b></p>
                    @php
                        $text = $data->description; // textarea value

                        $lines = explode("\n", $text);

                        echo '<ul style="list-style: bullet">';
                        foreach ($lines as $line) {
                            echo '<li>' . trim($line) . '</li>';
                        }
                        echo '</ul>';
                    @endphp
                </div>
                <div>
                    <h6 class="float-end">For {{ $setting->company_name }}</h6>

                    <p class="mt-5">Authorized Signatory</p>
                </div>

            </div>




        </div>

    </div>
@endsection
