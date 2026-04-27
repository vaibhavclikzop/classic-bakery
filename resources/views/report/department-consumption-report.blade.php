@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Department consumption report</title>
    @endpush

    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between">
                <div>

                    <h4>Department Consumption Report</h4>
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
                                <select name="department_id" id="department_id" class="form-control">
                                    <option value="">All Department </option>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('department_id') == $item->id ? 'selected' : '' }}>
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


        </div>
        <div class="card-body">
            <table class="table" id="exportTable">
                <thead>
                    <tr>
                        <th colspan="6" style="text-align: center"> From Date : {{request("fromDt")}} To Date : {{request("toDt")}} Department <span id="departName"></span> </th>
                    </tr>
                    <tr>
                        <th>S.No</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total Amount</th>
                        <th>Last Purchase Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ formatQtyPrice($item->qty) }}</td>
                            <td>{{ formatQtyPrice($item->price) }}</td>
                            <td>{{ formatQtyPrice($item->price * $item->qty) }}</td>
                            <td>{{ $item->last_purchase_price }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>


    </div>
    <script>
        $(document).ready(function() {
            $("#department_id").select2();

            $("#departName").text($("#department_id").find(":selected").text())
        })
    </script>
@endsection
