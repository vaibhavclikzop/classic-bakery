@extends('layouts.main')
@section('main-section')
@push('title')
<title>Expense Category</title>
@endpush
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Expense</h4>
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

                    <div>
                        <select name="outlet_id" id="outlet_id" class="form-control">
                            <option value="">Select Outlet</option>
                            @foreach ($outlet as $item)
                            <option value="{{ $item->id }}"
                                {{ request('outlet_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->outlet_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mx-1">
                        <Button class="btn btn-primary" type="submit">Search</Button>
                    </div>
                    <div>
                        <div>
                            <button id="exportToExcel" data-name="Department Consumption Report"
                                class="btn btn-success float-end btn-sm mx-2">Export
                                to Excel</button>

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
                    <th>S.no</th>
                    <th>Expence Category</th>
                    <th>Expence Sub Category</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Note</th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->category_name ?? '-' }}</td>
                    <td>{{ $item->sub_category_name ?? '-' }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->expense_date)->format('d-m-Y') }}</td>
                    <td>{{ $item->note }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection