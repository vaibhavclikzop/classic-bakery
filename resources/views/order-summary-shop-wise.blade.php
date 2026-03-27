@extends('layouts.main')
@section('main-section')
@push('title')
<title>Order Summary</title>
@endpush

<style>
    #PrintOrder {
        width: 100%;
        border: 1px solid #635e5e;
        padding: 15px;
        font-size: 12px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #777;
        padding: 5px;
    }

    .report-table th {
        background: #f2f2f2;
    }

    .total-row td {
        text-align: right;
        font-weight: bold;
    }

    @media print {

        body * {
            visibility: hidden;
        }

        #PrintOrder,
        #PrintOrder * {
            visibility: visible;
        }

        #PrintOrder {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        #PrintOrder table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #PrintOrder th,
        #PrintOrder td {
            border: 1px solid #635e5e !important;
            padding: 4px;
            font-size: 11px;
        }

        .report-table {
            width: 100% !important;
            border: 1px solid #635e5e !important;
            border-collapse: collapse !important;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #635e5e !important;
            padding: 4px;
        }


    }
</style>
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
                                {{ $item->outlet_name }}
                            </option>
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


        <div id="PrintOrder">

            <h2 style="text-align:center;margin-bottom:5px;">Shopwise Order Report</h2>

            <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                <div>
                    <strong>{{ $setting->company_name }}</strong>
                </div>

                <div>
                    <strong>Printed Date :</strong> {{ date('d-m-Y h:i A') }}
                </div>
            </div>

            <div style="margin-bottom:10px;">
                <strong>Delivery Date :</strong> {{ request('date') }}
            </div>

            <hr>

            <div style="display:flex;justify-content:space-between;margin-bottom:15px;">

                <div style="width:60%;">
                    <b>Shop Name :</b> {{ $customer_details->name ?? '' }} <br>
                    <b>Shop Address :</b> {{ $customer_details->address ?? '' }} <br>
                    <b>Contact No :</b> {{ $customer_details->number ?? '' }}
                    <br>
                    @foreach($selected_order as $order)
                    <b>Order No :</b> {{ $order->order_id }}
                    <b>Order Type :</b> {{ ucfirst($order->order_type_name) }} <br>
                    @endforeach
                </div>

            </div>

            @php
            $grouped = $work_order_det->groupBy('sub_category');
            $half = ceil($grouped->count()/2);
            $left = $grouped->slice(0,$half);
            $right = $grouped->slice($half);
            @endphp

            <div style="display:flex;gap:20px;align-items:flex-start;">

                {{-- LEFT COLUMN --}}
                <div style="width:50%;">

                    @foreach($left as $category => $items)

                    <div style="margin-bottom:15px;">

                        <h4 style="margin-bottom:5px;">Category : {{ $category }}</h4>

                        <table class="report-table" style="width:100%; border-collapse:collapse; border:1px solid #635e5e;">

                            <thead>
                                <tr>
                                    <th style="width:60%; border:1px solid #635e5e;">Product</th>
                                    <th style="width:20%; text-align:center; border:1px solid #635e5e;">Order Qty</th>
                                    <th style="width:20%; text-align:center; border:1px solid #635e5e;">Actual Qty</th>
                                </tr>
                            </thead>

                            <tbody>

                                @php $total = 0; @endphp

                                @foreach($items as $item)

                                <tr>
                                    <td style="border:1px solid #635e5e;">{{ $item->product }}</td>
                                    <td style="text-align:center; border:1px solid #635e5e;">
                                        {{ formatQtyPrice($item->qty) }}
                                    </td>
                                    <td style="border:1px solid #635e5e;"></td>
                                </tr>

                                @php $total += $item->qty; @endphp

                                @endforeach

                                <tr>
                                    <td style="text-align:right; border:1px solid #635e5e;"><b>Total</b></td>
                                    <td style="text-align:center; border:1px solid #635e5e;"><b>{{ number_format($total,2) }}</b></td>
                                    <td style="border:1px solid #635e5e;"></td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    @endforeach

                </div>


                {{-- RIGHT COLUMN --}}
                <div style="width:50%;">

                    @foreach($right as $category => $items)

                    <div style="margin-bottom:15px;">

                        <h4 style="margin-bottom:5px;">Category : {{ $category }}</h4>

                        <table class="report-table">

                            <thead>
                                <tr>
                                    <th style="width:60%;text-align:center; border:1px solid #635e5e;"   >Product</th>
                                    <th style="width:20%;text-align:center;  border:1px solid #635e5e;">Order Qty</th>
                                    <th style="width:20%;text-align:center;  border:1px solid #635e5e;">Actual Qty</th>
                                </tr>
                            </thead>

                            <tbody>

                                @php $total = 0; @endphp

                                @foreach($items as $item)

                                <tr>
                                    <td  style="  border:1px solid #635e5e;">{{ $item->product }}</td>
                                    <td  style="text-align:center; border:1px solid #635e5e;">
                                        {{ formatQtyPrice($item->qty) }}
                                    </td>
                                    <td  style="text-align:center; border:1px solid #635e5e;"></td>
                                </tr>

                                @php $total += $item->qty; @endphp

                                @endforeach

                                <tr>
                                    <td style="text-align:right; border:1px solid #635e5e;"><b>Total</b></td>
                                    <td style="text-align:center; border:1px solid #635e5e;"><b>{{ number_format($total,2) }}</b></td>
                                    <td style="border:1px solid #635e5e;"></td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>
<script>
    $(document).ready(function() {
        $("select").select2();

        $("#customer_id").on("change",function(){
    
            $("#date").trigger("change")
        })

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