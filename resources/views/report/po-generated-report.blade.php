@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Po Generated Report</h4>
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
                    <th>Name</th>
                    <th>PO ID</th>
                    <th>Vendor Name</th>
                    <th>Status</th>
                    <th>User Name</th>
                    <th>Created at</th>
                   
                </tr>
            </thead>

            <tbody>
                @php
                $sno = 1;
                @endphp
                @foreach ($po_mst as $item)
                <tr>
                    <td>{{ $sno++ }}</td>
                    <td>{{ $item->name }}</td>

                    <td>{{ $item->po_id }}</td>
                    <td style="white-space: normal;">
                        {{ $item->vendor_name }}
                    </td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->user_name }}</td>

                   <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection