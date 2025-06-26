@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Audit Setting Raw Material</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Audit Setting Raw Material</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">


            <form action="" class="row" method="GET">
                <div class="col-md-4">
                    <label for="">Select Category</label>
                    <select class="form-control" name="category_id" id="category_id" required onchange="this.form.submit()">
                        <option value="">Select Category</option>
                        @foreach ($category as $item)
                            <option value="{{ $item->id }}" {{ request('category_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-4">
                    <label for="">Select Sub Category</label>
                    <select class="form-control" name="sub_category_id" id="sub_category_id" required
                        onchange="this.form.submit()">
                        <option value="">Select Sub Category</option>
                        @foreach ($sub_category as $item)
                            <option value="{{ $item->id }}"
                                {{ request('sub_category_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>

                </div>
              

            </form>


            <form method="POST" action="{{ route('SaveAuditReport') }}" class="row needs-validation" novalidate
                enctype="multipart/form-data">
                @csrf
                <div class="col-md-12 my-2">
                    <label for="">Remarks (If any)</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Enter remarks if any">

                </div>
                <input type="hidden" name="category_id" value="{{request("category_id")}}">

                <div class="col-md-12 mt-3">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th><input type="checkbox" id="all_check"></th>
                                <th>Name</th>
                                <th>Article No</th>
                                <th>Current Stock</th>
                                <th>Updated at</th>
                            </tr>

                        </thead>
                        <tbody id="prod_list">
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($products as $item)
                                <tr>
                                    <td>{{ $sno++ }}</td>
                                    <td> <input type="checkbox" class="all_check" name="check[]"
                                            value="{{ $item->id }}"> </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->article_no }}</td>
                                    <td>{{ $item->stock }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

                <div class="col-md-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary">Submit</button>

                </div>

            </form>
        </div>

    </div>

    <script>
        $("#location_id").on("change", function() {

            $.ajax({
                url: "/GetCSProducts",
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
                    var sno = 1;
                    result.forEach(element => {
                        html += `
                                    <tr>
                                        <td>${sno++}</td>
                                        <td> <input type="checkbox" class="all_check" name="check[]" value="${element.id}"> </td>
                                        <td>${element.name}</td>
                                        <td>${element.article_no}</td>
                                        <td>${element.stock}</td>
                                        <td>${element.updated_at}</td>
                                    </tr>
                                `;

                    });
                    $("#prod_list").html(html)
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
            if ($(this).prop("checked")) {
                $(".all_check").prop("checked", true)
            } else {
                $(".all_check").prop("checked", false)
            }
        })
    </script>
@endsection
