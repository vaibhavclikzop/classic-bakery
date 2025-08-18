@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Outward order list </title>
    @endpush
    <div class="card">
        <div class="card-header ">
            <form action="">
                <div class="d-flex justify-content-between">


                    <div class="page-title">
                        <h4>Outward order list</h4>
                    </div>
                    @php
                        $status = request('status');
                    @endphp
                    <div>
                        <a class="btn btn-primary"
                            href="outward-customer-order-list?status=dispatch&date={{ request('date') }}">
                            Dispatch
                        </a>
                        <a class="btn btn-primary"
                            href="outward-customer-order-list?status=delivered&date={{ request('date') }}">
                            Delivered
                        </a>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="d-flex mt-3 col-3 fliat">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <a class="btn btn-info"
                            href="?status={{ request('status') }}&date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                            << </a>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ request('date') ?? date('Y-m-d') }}" onchange="this.form.submit()">
                                <a class="btn btn-info"
                                    href="?status={{ request('status') }}&date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                    >>
                                </a>
                    </div>
                    <div class="d-flex mx-3">
                        <div class="col-5 mt-3">
                            <label for="">Customer Type</label>
                            <select name="order_type" id="order_type" class="form-control select2" required>
                                <option value="">Select</option>
                                <option value="customer" {{ request('order_type') == 'customer' ? 'selected' : '' }}>
                                    Customer
                                </option>
                                <option value="outlet" {{ request('order_type') == 'outlet' ? 'selected' : '' }}>Outlet
                                </option>


                            </select>

                        </div>
                        <div class="col-5 mt-3 mx-2">
                            <label for="">Customer / Outlet</label>
                            <select name="customer_id" id="customer_id" class="form-control select2" required
                                onchange="this.form.submit()">
                                <option value="">Select customer</option>
                                @foreach ($customers as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">

            <form action="{{ route('convertInvoiceDelivered') }}" method="POST" id="bulkUpdateForm">
                @csrf
                <button class="btn btn-dark btn-sm" type="button" id="bulkUpdate">Bulk Update</button>
                <input type="hidden" id="updateType" name="updateType">
                <table class="table dataTable"  id="myTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th><input type="checkbox" id="allChecks"></th>
                            <th>Customer </th>


                            <th>Invoice Date </th>
                            <th>Delivery Date </th>
                            {{-- <th>Transport </th>
                            <th>Contact Person </th>
                            <th>Number </th>
                            <th>Vehicle No </th> --}}

                            <th>User </th>
                            <th>Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td><input type="checkbox" name="outward_ids[]" value="{{$item->id}}" class="allChecks"></td>
                                <td>{{ $item->customer }}</td>
                                <td>{{ $item->invoice_date }}</td>
                                <td>{{ $item->delivery_date }}</td>
                                {{-- <td>{{ $item->transport }}</td>
                                <td>{{ $item->contact_person }}</td>
                                <td>{{ $item->number }}</td>
                                <td>{{ $item->vehicle_no }}</td> --}}

                                <td>{{ $item->user }}</td>
                                <td>

                                    <a class="btn btn-primary btn-sm"
                                        href="/customer-outward-challan-view/{{ $item->id }}"><i class="fa fa-eye"
                                            aria-hidden="true"></i></a>
                                    @if ($status == 'dispatch')
                                        <button class="btn btn-dark btn-sm delivered" type="button"
                                            value="{{ $item->id }}">Delivered</button>
                                    @endif
                                    @if ($item->is_invoice == 0)
                                        <button class="btn btn-success btn-sm convertInvoice" type="button"
                                            value="{{ $item->id }}">Convert to Invoice</button>
                                    @endif

                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </form>
        </div>

    </div>


    <form action="{{ route('SaveCustomerOutwardStatus') }}" method="post">
        @csrf

        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delivered
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        You are going to delivered this challan.....
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('ConvertToInvoice') }}" method="post">
        @csrf

        <div class="modal fade" id="convertInvoice">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Convert to invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="cid" name="id">
                        You are going to convert this challan to invoice.....
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Bulk Update
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="radio" name="type" value="delivered" class="type"> Mark Delivered &nbsp;
                    <input type="radio" name="type" value="invoice" class="type"> Mark Invoice. &nbsp;
                    <input type="radio" name="type" value="both" class="type"> Mark Both &nbsp;
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="btnUpdateBulk">Save</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {

            $(".select2").select2();
        })
        $(document).on("click", ".delivered", function() {
            $("#id").val($(this).val())
            $("#modalId").modal("show")
        })
        $(document).on("click", ".convertInvoice", function() {
            $("#cid").val($(this).val())
            $("#convertInvoice").modal("show")
        });
        $("#order_type").on("change", function() {
            $.ajax({
                url: "/GetCustomerOutletList",
                type: "POST",
                data: {
                    order_type: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select----</option>';
                    html += '<option value="">All</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#customer_id").html(html)
                    $("#customer_id").val("{{ request('customer_id') }}")
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });
        $("#order_type").trigger("change");

        $("#allChecks").on("click", function() {
            if ($(this).prop("checked")) {
                $(".allChecks").prop("checked", true);
            } else {
                $(".allChecks").prop("checked", false);
            }
        });
        $("#bulkUpdate").on("click", function() {
            if ($(".allChecks:checked").length === 0) {
                toastr.error("Please select at least one record.");
                return;
            }

            $("#bulkUpdateModal").modal("show")
        });
        $(".type").on("click", function() {
            $("#updateType").val($(this).val())
        });

        $("#btnUpdateBulk").on("click", function() {
            if ($("#updateType").val() == false) {
                toastr.error("Select update type");
                return
            }
            $("#bulkUpdateForm").submit();
        })
    </script>
@endsection
