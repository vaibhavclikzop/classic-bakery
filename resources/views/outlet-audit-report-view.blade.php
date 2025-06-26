@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Audit Report Raw Material</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Audit Report Raw Material</h4>
            </div>
            <div class="">


                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                    aria-hidden="true"></i> Print</button>



            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Physical Stock</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($audit_report_det as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->current_stock }}</td>
                            <td>{{ $item->stock }}</td>
                            @if ($item->status=="pending")
                            <td><span class="badge bg-danger"> {{ $item->status }}</span></td>
                            @else
                            <td><span class="badge bg-success"> {{ $item->status }}</span></td>
                            @endif
                         
                            <td>{{ $item->user }}</td>

                            <td>
                                @if ($item->status=="pending")
                                <button type="button" class="btn btn-primary btn-sm edit" value="{{ $item->id }}"><i
                                    class="fa fa-pencil" aria-hidden="true"></i></button>
                                @else
                              <span class="badge bg-success"> Audited</span>
                                @endif
                           
                            </td>
                        </tr>
                    @endforeach


                </tbody>

            </table>
        </div>

    </div>


    <form action="{{ route('SaveOutletAudit') }}" method="POST" class="needs-validation" novalidate>
        <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        @csrf
                        <label for="">Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control" required>
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
        $(document).on("click",".edit",function(){
            $("#id").val($(this).val())
            $("#modalId").modal("show")
        })
    </script>
@endsection
