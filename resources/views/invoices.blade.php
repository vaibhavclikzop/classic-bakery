@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Invoices</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Invoices</h4>
            </div>
            @php
                $status = request('status');
            @endphp

            <div>
                <form action="" method="GET" class="d-flex">
                    <div>
                        <label for="">From</label>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDt') ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">

                    </div>

                    <div>
                        <label for="">To</label>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()"
                            value="{{ request('toDt') ?? \Carbon\Carbon::now()->toDateString() }}">

                    </div>
                </form>

            </div>
            <div>
                
            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer </th>


                        <th>Invoice No </th>
                        <th>Invoice Date </th>
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
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->transport }}</td>
                            <td>{{ $item->contact_person }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->vehicle_no }}</td>

                            <td>{{ $item->user }}</td>
                            <td>

                                <a class="btn btn-primary btn-sm" href="/invoice-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>



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
