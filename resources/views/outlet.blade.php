@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Outlet List</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Outlet</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Outlet Name</th>
                        <th>Contact person name</th>
                        <th>Nick name</th>
                        <th>Number</th>

                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($outlet as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->outlet_name }}</td>
                            <td>{{ $item->contact_person }}</td>
                            <td>{{ $item->nickname }}</td>
                            <td>{{ $item->number }}</td>

                            <td>{{ $item->address }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->state }}</td>
                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-outlet_name="{{ $item->outlet_name }}"
                                    data-contact_person="{{ $item->contact_person }}" data-number="{{ $item->number }}"
                                    data-address="{{ $item->address }}" data-state="{{ $item->state }}"
                                    data-city="{{ $item->city }}"
                                    data-nickname="{{ $item->nickname }}"
                                    data-customer_type_id="{{ $item->customer_type_id }}"><i class="fa fa-pencil"
                                        aria-hidden="true"></i></button>
                                <a class="btn btn-sm btn-success" href="/outlet-product/{{ $item->id }}"> <i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveOutlet') }}">
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
                        <div class="col-md-6">
                            <label for="" class="form-label">Select Customer Type</label>
                            <select name="customer_type_id" id="customer_type_id" required class="form-control">
                                <option value="">Select</option>
                                @foreach ($customer_type as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label"> Outlet Name</label>
                            <input type="text" name="outlet_name" id="outlet_name" class="form-control" required>

                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Name</label>
                            <input type="text" name="contact_person" id="contact_person" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-4">
                            <label for="">Nickname</label>
                            <input type="text" name="nickname" id="nickname" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-4">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-4">
                            <label for="">Password</label>
                            <input type="text" name="password" id="password" class="form-control" required>
                        </div>



                        <div class="col-md-12 mt-4">
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>


                        <div class="col-md-6 mt-4">
                            <label for="">State</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">---Select State---</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">City</label>
                            <select name="city" id="city" class="form-control">
                                <option value="">---Select City---</option>
                            </select>
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
            $("#outlet_name").val($(this).data("outlet_name"));
            $("#contact_person").val($(this).data("contact_person"));
            $("#number").val($(this).data("number"));

            $("#address").val($(this).data("address"));
            $("#state").val($(this).data("state"));
            $("#city").html("<option value=" + $(this).data("city") + ">" + $(this).data("city") + "</option>");

            $("#customer_type_id").val($(this).data("customer_type_id"));
            $("#nickname").val($(this).data("nickname"));


            $("#modal_name").text("Update Outlet");


            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Outlet");



            $("#id").val("");

            $("#exampleModal").modal("show");
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

        })
    </script>
@endsection
