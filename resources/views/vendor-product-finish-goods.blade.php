@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Vendor Product</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4 class="">Vendor Product List <br> </h4>
                <span  >Vendor Name : {{ $vendor->name }}</span> <br>
                <span >Vendor Email : {{ $vendor->email }}</span> <br>
                <span  >Vendor Contact : {{ $vendor->number }}</span> <br>

            </div>
            <div class="">


                <button type="button" class="btn btn-dark" id="AddProduct">Add Product</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                    
                  
                        <th> Category</th>
                        <th> Sub Category</th>
                        <th>Product Name</th>
                        <th>Article Code</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($vendor_product as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                 
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->sub_category }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->article_no }}</td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>


    <div class="modal fade" id="modalId">
        <div class="modal-dialog  modal-dialog-scrollable modal-xl">
            <form method="POST" action="{{ route('AllocateFinishGoodsProduct') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Products
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table dataTable">
                            <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">

                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th><input type="checkbox" class="product_id" id="selectall"></th>
                              
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                 
                                    <th>Product</th>
                                    <th>Article Code</th>
                                    <th>Min Stock</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($products as $item)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td><input type="checkbox" class="checks" name="product_id[]"
                                                value="{{ $item->id }}"></td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->sub_category }}</td>
                                   
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->article_no }}</td>
                                        <td>{{ $item->min_stock }}</td>
                                        <td>{{ $item->price }}</td>
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
