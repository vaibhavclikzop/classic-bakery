@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Vendor </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Vendor List</h4>
            </div>
            <div class="">

                <button type="button" class="btn btn-dark float-end mx-2" data-bs-toggle="modal"
                    data-bs-target="#importModal"><i class="fa fa-download"></i> Import Vendor</button>
                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Vendor </button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Company Name</th>
                        <th>Name</th>
                        <th>Number</th>

                        <th>Email</th>
                        <th>GST</th>
                        <th>Address</th>

                        <th>City</th>
                        <th>State</th>
                        <th>Pincode</th>
                        <th>Active</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($vendor as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->company_name }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->gst }}</td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->state }}</td>

                            <td>{{ $item->pincode }}</td>
                            @if ($item->active == 1)
                                <td><span class="badge badge-success">Active</span></td>
                            @else
                                <td><span class="badge badge-danger">InActive</span></td>
                            @endif

                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-company_name="{{ $item->company_name }}" data-name="{{ $item->name }}"
                                    data-number="{{ $item->number }}" data-email="{{ $item->email }}"
                                    data-gst="{{ $item->gst }}" data-address="{{ $item->address }}"
                                    data-state="{{ $item->state }}" data-city="{{ $item->city }}"
                                    data-pincode="{{ $item->pincode }}" data-active="{{ $item->active }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>
                                <a href="{{ url('vendor-product', [$item->id]) }}" class="btn btn-primary btn-sm">Allocate
                                    Raw Material</a>
                                <a href="{{ url('vendor-product-finish-goods', [$item->id]) }}"
                                    class="btn btn-primary btn-sm">Allocate Finish Goods</a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveVendor') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Vendor </span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">


                        <div class="col-md-12">
                            <label for="validationCustom01" class="form-label"> Company Name</label>
                            <input class="form-control" id="company_name" name="company_name" required>



                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-4">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">GST</label>
                            <input type="text" name="gst" id="gst" class="form-control">
                        </div>

                        <div class="col-md-12 mt-4">
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>


                        <div class="col-md-6 mt-4">
                            <label for="">State</label>
                            <select name="state" id="state" class="form-control" required>
                                <option value="">---Select State---</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">City</label>
                            <select name="city" id="city" class="form-control" required>
                                <option value="">---Select City---</option>
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Pincode</label>
                            <input type="number" name="pincode" id="pincode" class="form-control">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Active</label>
                            <select name="active" id="active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
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

    <form action="{{ route('ImportVendor') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Import Vendor</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <div>
                                <a class="btn btn-success" href="/import-vendor.csv"
                                    download="/import-vendor.csv">Download Sample File</a>
                            </div>

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Instructions</strong>
                                </div>
                                <div class="mx-3">
                                    <ul style="list-style:decimal">
                                        <li>First download sample file.</li>
                                        <li>Add your data in sample file.</li>
                                        <li>Choose file and upload.</li>



                                    </ul>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#company_name").val($(this).data("company_name"));
            $("#name").val($(this).data("name"));
            $("#number").val($(this).data("number"));
            $("#email").val($(this).data("email"));
            $("#gst").val($(this).data("gst"));
            $("#address").val($(this).data("address"));
            $("#state").val($(this).data("state"));
            $("#city").val($(this).data("city"));
            $("#pincode").val($(this).data("pincode"));
            $("#active").val($(this).data("active"));
            $("#modal_name").text("Update customers");
            $("#city").html('<option value=' + $(this).data("city") + '>' + $(this).data("city") + '</option>');
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Vendor ");


            $("input, select, textarea").not('[name="_token"]').val("");

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
