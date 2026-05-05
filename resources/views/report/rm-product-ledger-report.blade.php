@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>RM Product ledger Report</h4>
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
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="">Select Products</option>
                            @foreach ($products as $item)
                            <option value="{{ $item->id }}"
                                {{ request('product_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
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
                    <th>S.No</th>
                    <th>Particular</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>In Qty</th>
                    <th>Out Qty</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->particular }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i A') }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ formatQtyPrice($item->in_qty) }}</td>
                    <td>{{ formatQtyPrice($item->out_qty) }}</td>
                    <td>{{ formatQtyPrice($item->balance) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#product_id").select2();
        $("#productName").text($("#product_id").find(":selected").text())
    })
</script>
@endsection