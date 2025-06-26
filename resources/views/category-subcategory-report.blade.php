@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Category Sub Category Report</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Category Sub Category Report</h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">Category</label>
                        <select name="f_category_id" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select</option>
                            @foreach ($category as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id == request('f_category_id') ? 'Selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mx-3">
                        <label for="">Sub Category</label>
                        <select name="f_sub_category_id" id="" class="form-control" onchange="this.form.submit()">
                            <option value="">Select</option>
                            @foreach ($sub_category as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id == request('f_sub_category_id') ? 'Selected' : '' }}>{{ $item->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') }}">
                    </div>

                    <div>
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') }}">
                    </div>
                </form>

            </div>
            <div>

                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="page-title">
                <h4>Category Sub Category Report</h4>
            </div>
            <table class="table dataTable ">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Name</th>
                        <th>Sale Qty</th>
                        <th>Amount</th>
                        <th>Return Qty</th>
                    </tr>
                </thead>
                @php
                    $sno = 1;
                @endphp
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->sub_category }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->return_qty }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
@endsection
