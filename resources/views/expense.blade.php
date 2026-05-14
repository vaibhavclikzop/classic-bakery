@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Expense Category</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Expense</h4>
            </div>
            <div>
                <form action="" class="d-flex">
                    <div>
                        <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                    </div>
                    <div class="mx-1">
                        <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                    </div>

                    <div>
                        <select name="outlet_id" id="outlet_id" class="form-control">
                            <option value="">Select Outlet</option>
                            @foreach ($outlet as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('outlet_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->outlet_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mx-1">
                        <Button class="btn btn-primary" type="submit">Search</Button>
                    </div>
                    <div>
                        <div>
                            <button id="exportToExcel" data-name="Department Consumption Report"
                                class="btn btn-success float-end btn-sm mx-2">Export
                                to Excel</button>

                        </div>
                    </div>
                </form>
            </div>

            <div>
                <div>
                    <button class="btn btn-success btn-sm btnUpdate" value="approved" type="button">Approve</button>
                    <button class="btn btn-danger btn-sm btnUpdate" value="reject" type="button">Reject</button>
                </div>

            </div>
        </div>

        <div class="card-body">
            <table class="table" id="exportTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> <input type="checkbox" id="allCheck"> </th>
                        <th>Expense Category</th>
                        <th>Expense Sub Category</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Note</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td> <input type="checkbox" name="checks[]" value="{{ $item->id }}" class="allCheck"> </td>
                            <td>{{ $item->category_name ?? '-' }}</td>
                            <td>{{ $item->sub_category_name ?? '-' }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->amount }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expense_date)->format('d-m-Y') }}</td>
                            <td>{{ $item->note }}</td>
                            <td>
                                @if ($item->status == 'pending')
                                    <span class="badge bg-primary">Pending</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form action="{{ route('updateExpenseStatus') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="updateModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="status" id="updateStatus" hidden>
                        <input type="hidden" name="ids" id="IDs">
                        <h4> Are you sure you want to <span id="statusText"></span> this expense?</h4>

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
        $("#allCheck").on("click", function() {
            $(".allCheck").prop("checked", $(this).prop("checked")).trigger("change");
        });
        $(document).on("click", ".btnUpdate", function() {
            $("#statusText").text($(this).val())
            $("#updateStatus").val($(this).val())

            let checks=$("#IDs").val();
            if (checks==false) {
                toastr.error("Choose at least one expense");
                return;
            }
            $("#updateModal").modal("show")
        });
        $(".allCheck").on("change", function() {

            let ids = [];

            $(".allCheck:checked").each(function() {
                ids.push($(this).val());
            });

            $("#IDs").val(ids.join(","));
        });
    </script>
@endsection
