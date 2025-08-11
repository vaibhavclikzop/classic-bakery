@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Orders</title>
    @endpush
    <div class="card">

        <div class="card-header ">
            <div class="d-flex justify-content-between">


                <div class="page-title">
                    <h4>Orders</h4>
                </div>



                <div class="">

                    @if ($status == 'processing')
                        {{-- <a href="/order-summary-department-wise?id={{ $department->id }}" class="btn btn-primary mx-1">Order
                        Summary Department Wise</a> --}}
                        <a href="/order-summary-customer-wise?id={{ $department->id }}" class="btn btn-info mx-1">Department
                            wise order report
                        </a>
                        <a href="/order-summary-shop-wise?id={{ $department->id }}" class="btn btn-info mx-1">Shop wise
                            order
                            report
                        </a>
                        <button class="btn btn-dark" type="button" data-bs-toggle="modal"
                            data-bs-target="#CompleteProduction">Production Complete </button>
                    @endif

                </div>
            </div>
            <div>
                <form action="" class="d-flex">

                    <div class="d-flex mt-3 col-3 fliat">
                        <a class="btn btn-info" href="?date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                            << </a>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ request('date') ?? date('Y-m-d', strtotime('+1 day')) }}"
                                    onchange="this.form.submit()">
                                <a class="btn btn-info"
                                    href="?date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                    >>
                                </a>

                    </div>
                    <div>
                        <select name="type" onchange="this.form.submit()" class="form-control mx-3 mt-3">

                            <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @if ($status == 'pending')
            <form method="POST" action="{{ route('GenerateWorkOrder') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                @csrf
                <button class="btn btn-dark float-end">Proceed Order</button>
                <table class="table ">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checks" checked> </th>
                            <th>S.no</th>
                            <th> Order ID</th>
                            <th> Customer Name</th>
                            <th> Order Type</th>

                            <th>Order Date</th>
                            <th>Delivery Date</th>


                            <th>User</th>

                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($orders as $item)
                            <tr>
                                <th>
                                    <input type="checkbox" checked name="order_ids[]" value="{{ $item->id }}"
                                        class="checks">
                                </th>
                                <th>{{ $sno++ }}</th>
                                <th>{{ $item->order_id }}</th>
                                <th>{{ $item->customer }}</th>
                                <th>{{ $item->category }}</th>

                                <th>{{ $item->order_date }}</th>
                                <th>{{ $item->delivery_date }}</th>


                                <th>{{ $item->user }}</th>

                                <th>



                                    @if ($item->status == 'dispatch' || $item->status == 'pending')
                                        <a href="/outward-customer-order?id={{ $item->id }}&customer_id={{ $item->customer_id }}"
                                            class="btn btn-secondary btn-sm">Outward</a>
                                    @endif


                                    <a href="/order-view/{{ $item->id }}" class="btn btn-secondary btn-sm"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                </th>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </form>
        @else
            <div class="card-body">
                <form action="{{ route('CompleteProduction') }}" method="POST" id="formMain">

                    @csrf
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th><input type="checkbox" id="checks"></th>
                                <th> Order ID</th>
                                <th> Order Type</th>
                                <th> Customer Name</th>


                                <th>Delivery Date</th>


                                <th>User</th>

                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($orders as $item)
                                <tr>
                                    <th>{{ $sno++ }}</th>
                                    <th><input type="checkbox" name="order_ids[]" value="{{ $item->id }}"
                                            class="checks">
                                    </th>
                                    <th>{{ $item->order_id }}</th>
                                    <th>{{ $item->category }}</th>
                                    <th>{{ $item->customer }}</th>


                                    <th>{{ $item->delivery_date }}</th>


                                    <th>{{ $item->user }}</th>

                                    <th>



                                        {{-- @if ($item->status != 'complete')
                                     <a class="btn btn-sm btn-info" href="/outward-order">Outward</a>
                                @endif --}}


                                        <a href="/order-view/{{ $item->id }}" class="btn btn-secondary btn-sm"><i
                                                class="fa fa-eye" aria-hidden="true"></i></a>
                                        @if ($item->status == 'dispatch' || $item->status == 'pending')
                                            <a href="/outward-customer-order?id={{ $item->id }}&customer_id={{ $item->customer_id }}"
                                                class="btn btn-secondary btn-sm">Outward</a>
                                        @endif
                                        @if ($item->status == 'pending')
                                            <button type="button" class="btn btn-danger btn-sm cancel"
                                                value="{{ $item->id }}">Cancel Order</button>
                                        @endif

                                    </th>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </form>
            </div>
        @endif



    </div>

    <form action="{{ route('SaveOrderStatus') }}" class="needs-validation" novalidate method="POST">
        @csrf
        <div class="modal fade" id="statusModal">
            <div class="modal-content modal-dialog">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Order Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <label for="">Select Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="processing">Processing</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="delivered">Delivered</option>
                    </select>
                    <div class="mt-3 dispatch_div">
                        <label for="">Dispatch Date</label>
                        <input type="date" name="dispatch_date" id="dispatch_date" class="form-control">

                    </div>
                    <div class="mt-3 delivered_div">
                        <label for="">Delivered Date</label>
                        <input type="date" name="delivered_date" id="delivered_date" class="form-control">

                    </div>
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


    <form action="{{ route('ShiftOrder') }}" class="needs-validation" novalidate method="POST">
        @csrf
        <div class="modal fade" id="shiftCustomerModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Shift Customer
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="sid" name="id">
                        <label for="">Customers</label>
                        <select name="customer_id" id="" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

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
    <form action="" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="CompleteProduction" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Complete Production</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>You are going to complete production</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnCompleteProduction">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('CancelOrder') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Cancel Order</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="deleteId" name="id">
                        <h5>Are you sure you want to delete this order?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
            });

        $(".dispatch_div").hide()
        $(".delivered_div").hide()
        $(document).on("click", ".btnEdit", function() {
            $("#id").val($(this).data("id"))
            $("#status").val($(this).data("status"))
            var delivered_date = $(this).data("delivered_date")
            var dispatch_date = $(this).data("dispatch_date")

            var status = $(this).data("status");

            $("#dispatch_date").val(dispatch_date)
            $("#delivered_date").val(delivered_date)
            if (status == "dispatched") {
                $(".dispatch_div").show(500)
                $(".delivered_div").hide(500)

            } else if (status == "delivered") {
                $(".dispatch_div").hide(500)
                $(".delivered_div").show(500)
            } else {
                $(".dispatch_div").hide(500)
                $(".delivered_div").hide(500)

            }
            $("#statusModal").modal("show");
        });
        $("#status").on("change", function() {
            var status = $(this).val();
            if (status == "dispatched") {
                $(".dispatch_div").show(500)
                $(".delivered_div").hide(500)

            } else if (status == "delivered") {
                $(".dispatch_div").hide(500)
                $(".delivered_div").show(500)
            } else {
                $(".dispatch_div").hide(500)
                $(".delivered_div").hide(500)

            }
        });
        $(document).on("click", ".shiftOrder", function() {
            var id = $(this).data("id")
            $("#sid").val(id)
            $("#shiftCustomerModal").modal("show");
        });

        $(document).on("click", ".cancel", function() {
            $("#deleteId").val($(this).val())
            $("#cancelOrderModal").modal("show")
        });
        $("#checks").on("click", function() {
            if ($(this).prop("checked") === true) {
                $(".checks").prop("checked", true);
            } else {
                $(".checks").prop("checked", false);
            }
        });
        $("#btnCompleteProduction").on("click", function() {
            if ($(".checks:checked").length === 0) {
                alert("Select at least one order");
                return;
            }
            $("#formMain").submit();
        });
    </script>
@endsection
