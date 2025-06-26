@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order</h4>
            </div>
            <div>
                 <div>
                <form action="" method="GET">
                    <div class="d-flex">
                        <div>
                            <label for="">From</label>
                            <input type="date" name="fromDt" value="{{request("fromDt")}}" class="form-control" onchange="this.form.submit()">
                        </div>
                         <div>
                            <label for="">To </label>
                            <input type="date" name="toDt" value="{{request("toDt")}}" class="form-control" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>
            </div>
            <div class="">

                {{-- <form action="{{ url('purchase-order') }}/{{ $status }}" method="GET">

                    <select name="po" id="po" class="form-control" onchange="this.form.submit()">
                        <option value="1" {{ request('po') == 1 ? 'selected' : '' }}>All</option>
                        <option value="2" {{ request('po') == 2 ? 'selected' : '' }}>Direct PO</option>
                        <option value="3" {{ request('po') == 3 ? 'selected' : '' }}>Customer PO</option>
                    </select>
                </form> --}}
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
                            <td>{{ $item->vendor_name }}</td>
                            <td>{{ $item->user_name }}</td>
                     
                            <td>{{ date("d-m-Y h:i A ", strtotime($item->created_at)) }}</td>
                            <td>
                                @if ($status == 'pending')
                                    <button class="btn btn-sm btn-info editStatus" type="button"
                                        data-id="{{ $item->id }}">Generate PO</button>
                                @endif
                                <a class="btn btn-primary btn-sm" href="/purchase-order-view-finish-products/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

    <form action="{{ route('SaveFGGeneratePO') }}" method="POST" class="needs-validation" novalidate>
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
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter PO Name" required>

                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="">Description</label>
                                <textarea name="description" id="description" class="form-control" ></textarea>

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

    <script>
        $(document).ready(function() {
            $(document).on("click", ".editStatus", function() {
                $("#id").val($(this).data("id"))
                $("#modalId").modal("show")
            })
        })
    </script>
@endsection
