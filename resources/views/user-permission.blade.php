@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>User Permission</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">
                <h6>User Permission</h6>
                <br>
                <h3> {{ $role->name }}</h3>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('SaveUserPermission') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <table class="table">
                    <input type="hidden" name="role_id" value="{{ $id }}">
                    <tbody>
                        <tr>
                            <td>
                                <label for="">Select Permission</label>
                                <select name="permission_id" id="permission_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($permission_mst as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <label for="">View</label>
                                <select name="view" id="view" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                            <td>
                                <label for="">Edit</label>
                                <select name="edit" id="edit" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </td>
                            <td>

                                <button class="btn btn-success mt-4" type="submit">Add</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            @php
                $sno = 1;
            @endphp
            <table class="table mt-4">
                <tr>
                    <th>S.No</th>
                    <th>Permission</th>
                    <th>View</th>
                    <th>Edit</th>
                    <th>Action</th>
                </tr>
                @foreach ($role_permission as $item)
                    <tr>
                        <td>{{ $sno++ }}</td>
                        <td>{{ $item->permission }}</td>
                        @if ($item->view == 1)
                            <td><i class="fa fa-check-circle text-success" aria-hidden="true"></i></td>
                        @else
                            <td><i class="fa fa-times-circle text-danger" aria-hidden="true"></i></td>
                        @endif
                        @if ($item->edit == 1)
                            <td><i class="fa fa-check-circle text-success" aria-hidden="true"></i></td>
                        @else
                            <td><i class="fa fa-times-circle text-danger" aria-hidden="true"></i></td>
                        @endif
                        <td>
                            <button class="btn btn-danger btn-sm delete" data-id="{{ $item->id }}"><i
                                    class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>

                    </tr>
                @endforeach
            </table>

        </div>

    </div>

   <form action="{{ route('RemovePermission') }}" method="POST">
    @csrf
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Remove Permission
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    Are you sure you want to remove permission?
          
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
        $(document).on("click", ".delete", function() {
            $("#id").val($(this).data("id"))
            $("#modalId").modal("show")
        })
    </script>
@endsection
