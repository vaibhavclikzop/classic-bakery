@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Type</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Type</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Order Type</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Type</th>
                        <th> Name</th>
                        <th> Days</th>
                        <th> Week Days</th>
                        <th> Sub Category</th>


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

                            <td>{{ $item->type }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->days }}</td>
                            <td>{!! str_replace(',', '<br>', e($item->week_days)) !!}</td>
                            <td>{!! str_replace(',', '<br>', e($item->sub_category_name)) !!}</td>



                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}" data-days="{{ $item->days }}"
                                    data-order_days="{{ $item->week_days }}"
                                    data-f_sub_category_id="{{ $item->f_sub_category_id }}"
                                    data-type="{{ $item->type }}"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveOrderType') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Order Type</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-12">
                            <label for=""> Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>

                        </div>


                        <div class="col-md-12">
                            <label for="">Order Type</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Default Days</label>
                            <input type="number" name="days" id="days" class="form-control" required>

                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Week Days</label>
                            <select name="week_days[]" id="week_days" class="form-control" required multiple>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>

                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Sub Category</label>
                            <select name="f_sub_category_id[]" id="f_sub_category_id" class="form-control" required
                                multiple>
                                <option value="">Select</option>
                                @foreach ($sub_category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
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
        $(document).ready(function() {
            $("#week_days").select2()
            $("#f_sub_category_id").select2()
        })
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#brand_id").val($(this).data("brand_id"));
            $("#days").val($(this).data("days"));
            $("#type").val($(this).data("type"));
            $("#week_days").val($(this).data("order_days").split(", "));
            var subCategoryData = $(this).data("f_sub_category_id");

            // Ensure it's a string before processing
            if (typeof subCategoryData !== "string") {
                subCategoryData = String(subCategoryData); // Convert number to string
            }

            if (subCategoryData.trim() === "") {
                subCategoryData = [];
            } else {
                subCategoryData = subCategoryData.includes(",") ? subCategoryData.split(", ") : [subCategoryData];
            }

            console.log(subCategoryData);

            $("#f_sub_category_id").val(subCategoryData);
            $("#modal_name").text("Update Order Type");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Order Type");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });
    </script>
@endsection
