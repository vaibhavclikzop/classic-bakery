@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>RM Purchase History Report </h4>
        </div>

        <div>
            <div>
                <form action="" class="d-flex">
                    <div>
                        <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                    </div>
                    <div class="mx-1">
                        <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                    </div>

                    <div class="mx-1">
                        <Button class="btn btn-primary" type="submit">Search</Button>
                    </div>
                    <div>
                        <div>
                            <button id="exportToExcel" data-name="Po Generated Report"
                                class="btn btn-success float-end btn-sm mx-2">Export
                                to Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table" id="exportTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Product Name</th>
                    <th>Date</th>
                    <th>Vendor Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                </tr>
            </thead>

            <tbody>
                @foreach($data as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                    <td>{{ $item->vendor_name }}</td>
                    <td>{{ formatQtyPrice($item->price) }}</td>
                    <td>{{ formatQtyPrice($item->qty)}}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection