@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">
                <h4>Inward Report</h4>
            </div>
            <div>
                <form action="" class="d-flex">
                    <div>
                        <label for="">From </label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()" value="{{request("fromDt")}}">
                    </div>
                    <div>
                        <label for="">To </label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()" value="{{request("toDt")}}">
                    </div>

                </form>
            </div>
            <div>

            </div>


        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>PO</th>
                        <th>Vendor</th>

                        <th>Invoice No</th>
                        <th>Invoice</th>
                        <th>Invoice Date</th>
                        <th>R.M Date</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock_inward_mst as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->po_name }}</td>
                            <td>{{ $item->vendor }}</td>

                            <td>{{ $item->invoice_id }}</td>
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->received_material_date }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->user }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td><a class="btn btn-info btn-sm" href="/inward-report-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
@endsection
