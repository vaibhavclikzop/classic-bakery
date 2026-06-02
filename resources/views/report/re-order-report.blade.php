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

            <div class="">
                <form method="GET" class="d-flex  justify-content-center">
                    <div class="">
                        <label for="">Sub Category</label>
                        <select name="sub_category_id[]" multiple id="sub_category_id" class="form-control">
                            <option value="all">Select All</option>
                            @foreach ($sub_category as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="mx-2">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select All</option>
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
            <form action="{{ url('/generate-po') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div class="text-center mb-3">
                        <h4>Classic Bakery</h4>
                        <h5>Re-order Report</h5>
                    </div>
                    <div>
                        @if (request('vendor_id'))
                            <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}" hidden>
                            <button class="btn btn-primary">Generate PO</button>
                        @endif

                    </div>
                </div>


                <table class="table table-bordered table-sm w-100" id="exportTable">

                    <thead style="background:#f0f0f0">

                        <tr>

                            <th>S.No</th>
                            <th><input type="checkbox" id="allCheck"> </th>
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
                                <td> <input type="checkbox" class="allCheck" name="product_ids[]"
                                        value="{{ $item->product_id }}"> </td>
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

            </form>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            let sub_category_id = @json(request('sub_category_id'));

            if (sub_category_id) {
                $("#sub_category_id").val(sub_category_id)
            }

            $("#vendor_id, #sub_category_id").select2();


            $("#sub_category_id").on("change", function() {
                $.ajax({
                    url: "/reports/getVendorBySubCategory",
                    type: "POST",
                    data: {
                        id: $(this).val(),

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        var html = "<option value=''>Select All</option>";
                        result.forEach(element => {
                            html +=
                                `<option value="${element.id}">${element.vendor}</option>`;

                        });
                        $("#vendor_id").html(html)

                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });
            });

            $("#allCheck").on("click", function() {
                $(".allCheck").prop("checked", $(this).prop("checked"))
            })
        });
    </script>
@endsection
