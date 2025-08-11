@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Advance Order</h4>
            </div>
            <div class="">
                <form class="d-flex" method="GET">
                 
                        <a class="btn btn-info" href="?date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                            << </a>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ request('date') ?? date('Y-m-d', strtotime('+1 day')) }}"
                                    onchange="this.form.submit()">
                                <a class="btn btn-info"
                                    href="?date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                                    >>
                                </a>

            


                </form>

            </div>
        </div>
        <div class="card-body">
            <form action="">
                <div>
                    <button type="submit" value="printOrder" name="printOrder" class="btn btn-primary mx-2">Print Bulk
                        Order</button>
                </div>
                <div>
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th><input type="checkbox" id="all_check"></th>
                                <th>Outlet</th>
                                <th>Order Date</th>
                                <th>Delivery Date Time</th>
                                <th>Order Type</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            $sno = 1;
                        @endphp
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $sno++ }}</td>
                                    <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="all_check">
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->order_date }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->delivery_date . ' ' . $item->delivery_time)->format('d-m-Y g:i A') }}
</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="/advance-order-view/{{ $item->id }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i> </a>
                                        
                                        @if ($item->status != 'cancel'  )
                                        @if ($item->status != 'delivered' )
                                            <button class="btn btn-dark btn-sm change_status" value="{{ $item->id }}"
                                                data-status="{{ $item->status }}" type="button"><i class="fa fa-pencil"
                                                    aria-hidden="true"></i></button>
                                        @endif
                                        @endif
                                         @if ($item->status == 'pending')
                                            <button class="btn btn-danger btn-sm cancel_status" value="{{ $item->id }}"
                                                data-status="{{ $item->status }}" type="button"><i class="fa fa-xmark"
                                                    aria-hidden="true"></i></button>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

    </div>



    <form action="{{ route('UpdateStatus') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Change Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select</option>
                            <option value="dispatch">Dispatch</option>
                            <option value="complete">Out for Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancel">Cancel</option>
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

      <form action="{{ route('Cancel_order') }}" method="POST" class="needs-validation" novalidate>
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
        $("#all_check").on("click", function() {
            if ($(this).prop("checked") == true) {
                $(".all_check").prop("checked", true)
            } else {
                $(".all_check").prop("checked", false)
            }
        });

        $(document).on("click", ".change_status", function() {

            $("#id").val($(this).val())
            $("#status").val($(this).data("status"))
            $("#modalId").modal("show")
        });

        $(document).on("click", ".cancel_status", function() {
            $("#deleteId").val($(this).val())
            $("#cancelOrderModal").modal("show")
        });
    </script>
@endsection
