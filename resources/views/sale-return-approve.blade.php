@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Return Approve</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Return Approve</h4>
            </div>
            <div>

            </div>
            <div class="">






            </div>
        </div>
        <div class="card-body">
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

                        <p>
                            {{ $po_mst->customer }} <br>
                            {{ $po_mst->address }}, {{ $po_mst->state }}, {{ $po_mst->city }}, ,

                            {{ $po_mst->number }} <br>
                            {{ $po_mst->email }} <br>
                            {{ $po_mst->gst }} <br>


                            {{ $po_mst->return_date }} <br>

                        </p>

                    </div>
                </div>
            </div>
            <form action="{{ route('SaveSaleReturnApprove') }}" method="POST">
                @csrf
                <input name="mst_id" type="hidden" value="{{$po_mst->id}}">
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
                            <th>Type</th>

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
                                    <td>

                                        <select name="type[{{ $item->id }}][]" class="form-control">
                                            <option value="expired" {{ $item->type == 'expired' ? 'selected' : '' }}>
                                                Expired
                                            </option>
                                            <option value="current_stock"
                                                {{ $item->type == 'current_stock' ? 'selected' : '' }}>
                                                Current Stock</option>
                                            <option value="scrap" {{ $item->type == 'scrap' ? 'selected' : '' }}>Scrap
                                            </option>
                                            <option value="rejected" {{ $item->type == 'rejected' ? 'selected' : '' }}>
                                                Rejected
                                            </option>
                                        </select>
                                    </td>



                                </tr>
                            @endforeach

                        </tbody>


                    </table>
                </div>
                <div class="text-center mt-5">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>

    </div>
@endsection
