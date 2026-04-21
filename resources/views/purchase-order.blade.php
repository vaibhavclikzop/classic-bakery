@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order</h4>
            </div>
            <div>
                <form action="" method="GET">
                    <div class="d-flex">
                        <div>
                            <label for="">From</label>
                            <input type="date" name="fromDt" value="{{ request('fromDt') }}" class="form-control"
                                onchange="this.form.submit()">
                        </div>
                        <div>
                            <label for="">To </label>
                            <input type="date" name="toDt" value="{{ request('toDt') }}" class="form-control"
                                onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>PO ID</th>
                        <th>Vendor Name</th>
                        <th>User Name</th>


                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($po_mst as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->po_id }}</td>
                            <td style="white-space: normal;">
                                {{ $item->vendor_name }}
                            </td>

                            <td>{{ $item->user_name }}</td>

                            <td>{{ date('d-m-Y h:i A ', strtotime($item->created_at)) }}</td>
                            <td>
                                @if ($status == 'pending')
                                    <form method="POST" action="{{ route('SaveGeneratePO') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{ $item->id }}">
                                        <button class="btn btn-sm btn-info editStatus" type="submit">Generate PO</button>
                                        <button class="btn btn-danger btn-sm btnDelete" type="button"
                                            value="{{ $item->id }}"><i class="fa fa-trash"
                                                aria-hidden="true"></i></button>
                                    </form>
                                @endif
                                <a class="btn btn-primary btn-sm m-1" href="/purchase-order-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

    <form action="{{ route('SaveGeneratePO') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Generate PO
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter PO Name" required>

                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="">Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>

                            </div>

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

    <form action="{{ route('deletePO') }}" method="POST" class="needs-validation" novalidate>
        @csrf

    <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalTitleId">
                        Delete PO
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this PO?
                    <input type="hidden" id="deleteID" name="id" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

</form>

    <script>
        // $(document).ready(function() {
        //     $(document).on("click", ".editStatus", function() {
        //         $("#id").val($(this).data("id"))
        //         $("#modalId").modal("show")
        //     })
        // })

        $(document).on("click", ".btnDelete", function() {
            $("#deleteID").val($(this).val());
            $("#deleteModal").modal("show")
        })
    </script>
@endsection
