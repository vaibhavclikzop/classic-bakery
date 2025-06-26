@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Mode of Transport</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Mode of Transport</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add </button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Contact Person</th>
                        <th>Number</th>
                        <th>Vehicle No</th>


                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td>{{ $item->name }}</td>
                            <td>{{ $item->contact_person }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->vehicle_no }}</td>



                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"   
                                    data-contact_person="{{ $item->contact_person }}"
                                    data-number="{{ $item->number }}"
                                    data-vehicle_no="{{ $item->vehicle_no }}"
                                    
                                    ><i class="fa fa-pencil" aria-hidden="true"></i></button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveModeOfTransport') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Category</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">


                        
                        <div class="col-md-12">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Contact Person</label>
                            <input type="text" name="contact_person" id="contact_person" class="form-control">

                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control">

                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Vehicle Number</label>
                            <input type="" name="vehicle_no" id="vehicle_no" class="form-control">

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

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#contact_person").val($(this).data("contact_person"));
            $("#number").val($(this).data("number"));
            $("#vehicle_no").val($(this).data("vehicle_no"));
        
            $("#modal_name").text("Update Mode of Transport");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Mode of Transport");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });
    </script>
@endsection
