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
                    <a class="btn btn-primary" href="outward-customer-order-list?status=dispatch&date={{ request('date') }}">
                        Dispatch
                    </a>
                    <a class="btn btn-primary" href="outward-customer-order-list?status=delivered&date={{ request('date') }}">
                        Delivered
                    </a>
                </div>
            </div>

            <div>
                <div class="d-flex mt-3 col-3 fliat">
                    <input type="hidden" name="status" value="{{request('status')}}">
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
            </div>
        </form>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer </th>


                        <th>Invoice Date </th>
                        <th>Delivery Date </th>
                        <th>Transport </th>
                        <th>Contact Person </th>
                        <th>Number </th>
                        <th>Vehicle No </th>

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


                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->delivery_date }}</td>
                            <td>{{ $item->transport }}</td>
                            <td>{{ $item->contact_person }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->vehicle_no }}</td>

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

    <script>
        $(document).on("click", ".delivered", function() {
            $("#id").val($(this).val())
            $("#modalId").modal("show")
        })
        $(document).on("click", ".convertInvoice", function() {
            $("#cid").val($(this).val())
            $("#convertInvoice").modal("show")
        });
    </script>
@endsection
