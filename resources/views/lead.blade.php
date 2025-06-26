@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Lead</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Lead</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Lead</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Number</th>
                        <th> Email</th>
                        <th> GenSet</th>
                        <th> Classification</th>
                        <th> Status</th>
                        <th> Remind Date</th>
                        <th> Remind Time</th>
                        <th> Remakrs</th>
                        <th> User</th>
                        <th> Created at</th>
                        <th> Action</th>



                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($lead as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->genset_name }}</td>
                            <td>{{ $item->classification }}</td>
                            <td>{{ $item->status_name }}</td>
                            <td>{{ $item->remind_date }}</td>
                            <td>{{ $item->remind_time }}</td>
                            <td>{{ $item->remarks }}</td>
                            <td>{{ $item->user_name }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                <button class="btn btn-sm btn-success edit" type="button" data-id="{{ $item->id }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>
                                <button class="btn btn-sm btn-info ViewRemarks" type="button"
                                    data-id="{{ $item->id }}"><i class="fa fa-eye" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveLead') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add </span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">


                        <div class="col-md-4">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control">

                        </div>

                        <div class="col-md-4">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>

                        </div>

                        <div class="col-md-4">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control">

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">GenSet</label>
                            <select name="gen_set" id="gen_set" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($finish_products_mst as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Classification</label>
                            <select name="classification" id="classification" class="form-control">
                                <option value="">Select</option>
                                <option value="Hot">Hot</option>
                                <option value="Cold">Cold</option>
                                <option value="Warm">Warm</option>

                            </select>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($Leadstatus as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Remind Date</label>
                            <input type="date" name="remind_date" id="remind_date" class="form-control">

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Remind Time</label>
                            <input type="time" name="remind_time" id="remind_time" class="form-control">

                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" required></textarea>

                        </div>




                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="remarksModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Remarks
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Status</th>
                                <th>Remind Date</th>
                                <th>Remind Time</th>
                                <th>Remarks</th>
                                <th>User</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody id="remarksList">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>




    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));


            var id = $(this).data("id");
            $.ajax({
                url: "/GetLeadDetails",
                type: "POST",
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {


                    $.each(result, function(i, o) {
                        console.log(i, o)

                        $('input[name=' + i + ']').val(o);
                        $('select[name=' + i + ']').val(o);
                    })
                    $("#modal_name").text("Update Lead");
                    $("#exampleModal").modal("show");
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Lead");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });


        $(document).on("click", ".ViewRemarks", function() {



            var id = $(this).data("id");
            $.ajax({
                url: "/GetRemarks",
                type: "POST",
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {

                    var remarksList = "";
                    var sno = 1;
                    result.forEach(element => {
                        remarksList += `
                                    <tr>
                                        <td>${sno++}</td>
                                        <td>${element.status}</td>
                                        <td>${element.remind_date}</td>
                                        <td>${element.remind_time}</td>
                                        <td>${element.remarks}</td>
                                        <td>${element.user}</td>
                                        <td>${element.created_at}</td>
                                        </tr>
                                `;
                    });
                    $("#remarksList").html(remarksList)

                    $("#remarksModal").modal("show");
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });
    </script>
@endsection
