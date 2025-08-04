@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Summary</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Summary </h4>
            </div>
            <div class="d-flex">



                <button id="exportToExcel" data-name="order summary shop wise" class="btn btn-success float-end btn-sm">Export
                    to Excel</button>
                <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>

            </div>
        </div>
        <div class="card-body">
            <div class="row">


                <div class="col-12">
                    <form action="" method="GET" class="d-flex justify-content-center">
                        <div style="width: 100%">
                            <select name="type" id="type" class="form-control" required>

                                <option value="outlet" {{ request('type') == 'outlet' ? 'selected' : '' }}>Outlet</option>
                                <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer
                                </option>
                            </select>
                        </div>
                        <div style="width: 100%">


                            <select name="customer_id" id="customer_id" class="form-control mx-3">
                                <option value="">Select Customer</option>
                                @foreach ($outlet as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('customer_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->outlet_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="width: 100%">


                            <input type="date" class="form-control" name="date" id="date"
                                value="{{ request('date') }}">
                        </div>
                        <div style="width: 100%">


                            <select name="order_id[]" id="order_id" class="form-control" multiple>
                                <option value="">Select</option>

                            </select>
                        </div>
                        <div>


                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-3">
                Selected Order : @foreach ($selected_order as $item)
                    <span class="badge badge-primary" style="font-size: 14px">{{ $item->order_id }}</span>
                @endforeach
            </div>


            <div id="PrintOrder" class="mt-5">

                <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                    <div>
                        <h3>{{ $setting->company_name }}</h3>
                        <p>{!! $setting->address !!}
                            <br>
                            E-Mail : {{ $setting->email }} <br>
                            Phone : {{ $setting->number }} <br>
                            GST : {{ $setting->gst_no }}

                        </p>


                    </div>


                    <div>
                        <div style="text-align: right;">
                            @if ($customer_details)
                                <h4>{{ $customer_details->name }}</h4>
                                <p>
                                    {{ $customer_details->email }} <br>
                                    Delivery Date : {{ request('date') }} <br>


                                </p>
                            @else
                                <h4>No Data Found</h4>
                            @endif


                        </div>
                    </div>
                </div>

                <div class="">
                    <table class="table" id="exportTable">
                        <thead>
                            <tr>
                                <th>S.No</th>

                                <th>Sub Category</th>
                                <th>Product</th>
                                <th>Order Qty</th>
                                <th>Actual Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($work_order_det as $item)
                                <tr>
                                    <td>{{ $sno++ }}</td>

                                    <td>{{ $item->sub_category }}</td>
                                    <td>{{ $item->product }}</td>
                                    <td>{{ formatQtyPrice($item->qty) }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex mt-4 justify-content-between">
                    <div>
                        <p><b><u><i>Terms & Conditions</i></u></b></p>
                        <ol style="list-style:number;">


                        </ol>
                    </div>
                    <div>
                        <h6 class="float-end">For {{ $setting->company_name }}</h6>

                        <p class="mt-5">Authorized Signatory</p>
                    </div>

                </div>
            </div>


        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("select").select2();

            $("#date").on("change click", function() {
                var customer_id = $("#customer_id").val();
                var type = $("#type").val();
                var date = $(this).val();
                if (customer_id == false) {
                    $(this).val("")
                    toastr.error("Select Customer");
                    return;
                }


                $.ajax({
                    url: "/GetWordOrder",
                    type: "POST",
                    data: {
                        customer_id: customer_id,
                        date: date,
                        type: type,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        var html = "";
                        html += '<option value="">----Select Order----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '">(' + element
                                .order_type + ') ' + element
                                .order_id + ' </option>';
                        });
                        $("#order_id").html(html)
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });

            });

            $("#type").on("change", function() {
                $.ajax({
                    url: "/GetCustomerOutlet",
                    type: "POST",
                    data: {
                        type: $(this).val(),

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        var html = "";
                        html += '<option value="">----Select----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '">' + element
                                .name + '</option>';
                        });
                        $("#customer_id").html(html)
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            })
        })
    </script>
@endsection
