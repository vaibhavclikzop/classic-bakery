@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Department Product list </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4 class="">Department product list <br> </h4>
                <span> Name : {{ $department->name }}</span> <br>



            </div>
            <div class="">


                <button type="button" class="btn btn-dark" id="AddProduct">Add Product</button>

            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('UpdateCustomerTypePrice') }}" method="post">
                @csrf
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.no</th>


                            <th>Sub Category Name</th>
                            <th>Product Name</th>
                            <th>Article No</th>
                            <th>Price</th>
             
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($department_product as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>

                                <td>{{ $item->category }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->article_no }}</td>
                                <td>{{ $item->price }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade" id="modalId">
        <div class="modal-dialog  modal-dialog-scrollable">
            <form method="POST" action="{{ route('AllocateDepartmentProduct') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Sub Category
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <input type="hidden" name="department_id" value="{{ $department->id }}">

                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th><input type="checkbox" class="product_id" id="selectall"></th>
                                    <th>Name</th>

                           
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($category as $item)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td><input type="checkbox" class="checks" name="product_id[]"
                                                value="{{ $item->id }}"></td>
                                        <td>{{ $item->name }}</td>
                               

                                    
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $("#AddProduct").on("click", function() {


            $("#modalId").modal("show");
        })
        $(document).ready(function() {
            $('#selectall').on('click', function() {
                if ($(this).prop("checked")) {
                    $(".checks").prop("checked", true)
                } else {
                    $(".checks").prop("checked", false)
                }
            });


        });
    </script>
@endsection
