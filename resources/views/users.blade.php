@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Users</h4>
            </div>
            <div class="">
                <button class="btn btn-primary" type="button" id="Add">Add User</button>

            </div>
        </div>
        <div class="card-body" id="">

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Address</th>
                        <th>Last Login</th>
                        <th>Platform</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->user_type }}</td>
                            <td>{{ $item->address }}, {{ $item->city }}, {{ $item->state }}</td>
                            <td>{{ $item->last_login }}</td>
                            <td>{{ $item->platform }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit" data-id="{{ $item->id }}" type="button"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

    </div>

    <form action="{{ route('SaveUser') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="id" id="id" class="form-control">
        <div class="modal fade" id="modalId">
            <div class="modal-content modal-dialog modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-4">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>

                        </div>
                        <div class="col-md-4">
                            <label for="">Phone</label>
                            <input type="number" name="phone" id="phone" class="form-control">

                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">State</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">Select</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">City</label>
                            <select name="city" id="city" class="form-control">
                                <option value="">Select</option>


                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Pincode</label>
                            <input type="number" name="pincode" id="pincode" class="form-control">

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Password</label>
                            <input type="" name="password" id="password" class="form-control" required>

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">User Role</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($role as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach


                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Department</label>
                            <select name="department_id" id="department_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($department as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach


                            </select>
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
        $("#Add").on("click", function() {
            $("#id").val("")
            $("#modalId").modal("show");
        });
        $("#state").on("change", function() {
            $.ajax({
                url: "/GetCity",
                type: "POST",
                data: {
                    state: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select City----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.city + '">' + element.city +
                            '</option>';
                    });
                    $("#city").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });

        $(document).on("click", ".edit", function() {
            var id = $(this).data("id");

            $.ajax({
                url: "/GetUserDetails",
                type: "POST",
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    $.each(result, function(i, o) {
                        $('input[name=' + i + ']').val(o);
                        $('textarea[name=' + i + ']').val(o);
                        $('select[name=' + i + ']').val(o);
                        if (i == "city") {
                            $("#city").html("<option value=" + o + ">" + o + "</option>")
                        }

                    })

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

            $("#modalId").modal("show");
        });
    </script>
@endsection
