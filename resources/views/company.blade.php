@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Company List</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Company</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Company Name</th>
                        <th>Source</th>
                        <th>Reference</th>

                        <th>Phone</th>
                        <th>Business Type</th>
                        <th>Remarks</th>

                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($company as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->source }}</td>
                            <td>{{ $item->ref_name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->business_type }}</td>
                            <td>{{ $item->remarks }}</td>
                            <td><button class="btn btn-primary btn-sm edit" data-id="{{ $item->id }}"
                                    data-company_name="{{ $item->name }}" data-source="{{ $item->source }}"
                                    data-reference="{{ $item->ref_name }}" data-number="{{ $item->number }}"
                                    data-business_type="{{ $item->business_type }}" data-remarks="{{ $item->remarks }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveCompany') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Company</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="id" id="uid">
                        <div class="col-md">
                            <label for="validationCustom01" class="form-label"> Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>

                        </div>

                        <div class="col-md">
                            <label for="" class="form-label"> Business Type</label>
                            <select name="business_type" id="business_type" class="form-control">
                                <option value="">Select</option>
                                <option value="Food">Food</option>
                                <option value="Products">Products</option>
                                <option value="Builder">Builder</option>
                                <option value="Architect">Architect</option>
                                <option value="Retail Customer">Retail Customer</option>
                                <option value="Interior Designer">Interior Designer</option>
                            </select>

                        </div>

                        <div class="col-md">
                            <label for="" class="form-label"> Source</label>
                            <select name="source" id="source" class="form-control">
                                <option value="">Select</option>
                                <option value="Walk In">Walk In</option>
                                <option value="Reference">Reference</option>
                            </select>

                        </div>
                        <div class="col-md reference">
                            <label for="validationCustom01" class="form-label">Reference Name</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="">

                        </div>


                        <div class="col-md">
                            <label> Phone Number</label>
                            <input type="number" class="form-control" id="number" name="number" value="">

                        </div>
                        <div class="col-md">
                            <label for="validationCustom01" class="form-label"> Remarks</label>
                            <input type="text" class="form-control" id="remarks" name="remarks" value="">

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
            $("#uid").val($(this).data("id"));
            $("#company_name").val($(this).data("company_name"));
            $("#source").val($(this).data("source"));
            $("#number").val($(this).data("number"));
            $("#reference").val($(this).data("reference"));
            $("#business_type").val($(this).data("business_type"));
            $("#remarks").val($(this).data("remarks"));
            $("#modal_name").text("Update Company");

            if ($(this).data("source") == "Reference") {
                $(".reference").show();
            } else {
                $(".reference").hide();
            }
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Company");


            $("#company_name").val("");

            $("#exampleModal").modal("show");
        });
        $(".reference").hide();
        $("#source").on("change", function() {
            if ($(this).val() == "Reference") {
                $(".reference").show(500);
            } else {
                $(".reference").hide(500);
            }
        });
    </script>
@endsection
