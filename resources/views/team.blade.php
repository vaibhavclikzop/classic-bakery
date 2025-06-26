@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Team</h4>
            </div>
            <div class="">
                <button class="btn btn-primary" type="button" id="Add">Add Team</button>

            </div>
        </div>
        <div class="card-body" id="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($team as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td><button class="btn btn-primary btn-sm edit" data-id="{{ $item->id }}" type="button"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

    </div>

    <form action="{{ route('SaveTeam') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="id" id="id" class="form-control">
        <div class="modal fade" id="modalId">
            <div class="modal-content modal-dialog">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Team
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <table class="table mt-4">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>#</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($users as $item)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>
                                            <input type="checkbox" name="user_id[]" id="id{{$item->id}}" value="{{ $item->id }}">
                                        </td>
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

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


        $(document).on("click", ".edit", function() {
            var id = $(this).data("id");

            $.ajax({
                url: "/GetTeamMember",
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

                    $("#id").val(id)

                    $.each(result, function(teamName, members) {

                        $('#name').val(teamName);
                        $.each(members, function(index, member) {
                          
                            $("#id"+member.user_id).prop("checked", true);
                        });


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
