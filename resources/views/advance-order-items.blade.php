@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Items</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Items</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>

                        <th> Name </th>
      
                        <th> Discount</th>
                        <th> Margin</th>
                        <th> GST</th>

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
                            <td>
                                {{ $item->name }}
                                <button class="btn btn-outline-dark btn-sm ShowFlavour" type="button"
                                    value="{{ $item->id }}"> <i class="fa-solid fa-circle-info"></i>
                                </button>
                            </td>
             
                            <td>{{ $item->discount }}</td>
                            <td>{{ $item->margin }}</td>
                            <td>{{ $item->gst }}</td>
                            <td><button class="btn btn-primary btn-sm edit" type="button" data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}" data-category_id="{{ $item->category_id }}"
                                    data-discount="{{ $item->discount }}" data-margin="{{ $item->margin }}"
                                    data-gst="{{ $item->gst }}"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveAdvanceOrderItem') }}">
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


                        <div class="col-md-6 d-none">
                            <label for="">Category</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="1" selected>Select</option>
                               
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Discount</label>
                            <input type="number" step="0.01" name="discount" id="discount" value="0"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">Margin</label>
                            <input type="number" step="0.01" name="margin" id="margin" value="0"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">GST</label>
                            <select step="0.01" name="gst" id="gst" class="form-control" required>
                                <option value="">Select GST</option>
                                @foreach ($gst as $item)
                                    <option value="{{ $item->gst }}">{{ $item->gst }} </option>
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


    <form action="{{ route('UpdateAdvancedItem') }}" method="POST">
        @csrf
        <div class="modal fade" id="Flavour_details" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Flavour Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="mst_id" name="id">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="all_check"> </th>
                                    <th>Flavour</th>
                                    <th>Fix Rate</th>
                                    <th>Increment Rate</th>
                                </tr>
                            </thead>
                            <tbody id="list">

                            </tbody>
                        </table>
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
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#category_id").val($(this).data("category_id"));
            $("#discount").val($(this).data("discount"));
            $("#margin").val($(this).data("margin"));
            $("#gst").val($(this).data("gst"));
            $("#modal_name").text("Update Item");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Item");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $(document).on("click", ".ShowFlavour", function() {
            $("#mst_id").val($(this).val())
            $.ajax({
                url: "/GetFlavourDetails",
                type: "POST",
                data: {
                    id: $(this).val(),

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    result.forEach(element => {
                        var select = "";
                        if (element.active == 1) {
                            select = "checked";
                        }
                        html += `
                            <tr>
                                <td><input type="checkbox" class="all_check" name="check[]" value="${element.id}" ${select}> </td>
                                <td>${element.flavour} </td>
                                <td><input type="number" step="0.01" value="${element.fix_rate}" name="rate[${element.id}][]" class="form-control"> </td>
                                <td><input type="number" step="0.01" value="${element.increment_rate}"  name="rate[${element.id}][]" class="form-control"> </td>
                               
                            </tr>
                        `;
                    });
                    $("#list").html(html)
                    $("#Flavour_details").modal("show")
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });
        $("#all_check").on("click", function() {
            if ($(this).prop("checked") == true) {
                $(".all_check").prop("checked", true)
            } else {
                $(".all_check").prop("checked", false)
            }
        })
    </script>
@endsection
