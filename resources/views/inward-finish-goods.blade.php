@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Inward Finish Goods</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4>Inward Finish Products</h4>

                <a class="btn btn-dark mt-3" href="/direct-inward"> <i class="fa fa-download" aria-hidden="true"></i> Direct Inward</a>
                <a class="btn btn-dark mt-3" href="/direct-inward-challan"> <i class="fa fa-download" aria-hidden="true"></i> Direct Inward Challan</a>
            </div>
            <div class="">

                <form method="GET" class="needs-validation d-flex" novalidate>
                    <a class="btn btn-info"
                        href="inward-finish-goods?date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                        << </a>
                            <input type="date" name="date" class="form-control" required
                                value="{{ request('date') ?? date('Y-m-d') }}">
                            <a class="btn btn-info"
                                href="inward-finish-goods?date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                >>
                            </a>
                            <button type="submit" class="btn btn-primary mx-2">Search</button>
                        
                </form>


            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('SaveInwardFinishGoods') }}" method="POST">
                <input type="date" value="{{ request('date') ?? date('Y-m-d') }}" name="date" class="d-none">
                @csrf
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Sub Category</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>In Qty</th>
                            <th>Inward Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($finish_inward_det as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->sub_category }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->inward_qty }}</td>
                                <td>
                                    @if ($finish_inward_mst->status == 0)
                                        <input type="number" step="0.01" name="inward_qty[{{ $item->id }}][]"
                                            value="{{ $item->qty -$item->inward_qty}}" class="form-control">
                                    @else
                                        {{ $item->inward_qty }}
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
                @if ($finish_inward_mst)
                    @if ($finish_inward_mst->status == 0)
                        <div class="mt-3 text-center">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    @endif
                @endif

            </form>
        </div>

    </div>
@endsection
