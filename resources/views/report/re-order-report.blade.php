@extends('layouts.main')

@section('main-section')
    @push('title')
        <title>Re Order Report</title>
    @endpush

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <div class="page-title">
                <h4>Re Order Report</h4>
            </div>

            <div>
                <form method="GET" class="d-flex">

                    <div class="mx-2">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('vendor_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->company_name }} </option>
                            @endforeach
                        </select>

                    </div>
                    <div>
                        <button class="btn btn-primary mt-4" type="submit">Search</button>
                    </div>


                </form>
            </div>
            <div>
                <button id="exportToExcel" data-name="rm order report" class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>


            </div>

        </div>


        <div class="card-body table-responsive" id="PrintOrder">

            <div class="text-center mb-3">
                <h4>Classic Bakery</h4>
                <h5>Re-order Report</h5>
            </div>

            <table class="table table-bordered table-sm w-100" id="exportTable">

                <thead style="background:#f0f0f0">

                    <tr>

                        <th>S.No</th>
                        <th>Sub Category</th>
                        <th>Product Name</th>
                        <th>Min Stock</th>
                        <th>Stock</th>
                        <th>Re Order Qty</th>
                        <th>Vendor</th>


                    </tr>

                </thead>

                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->sub_category }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->min_stock }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->re_order_qty }}</td>
                            <td>{{ $item->vendor }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>

    </div>

    <script>
        $(document).ready(function() {
            $("#vendor_id").select2()
        })
    </script>
@endsection
