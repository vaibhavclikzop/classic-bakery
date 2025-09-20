@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4 justify-content-between">

                    <div class="d-flex">
                        <div>
                            <input type="search" class="form-control" name="search" value="{{ request('search') }}">
                        </div>
                        <div>
                            <button class="btn btn-info" type="submit">Search</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp
            <form action="{{ route('updateStock') }}" method="POST" >
                @csrf
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Product Name</th>
                            <th>Article No</th>
                            <th>Stock</th>
                            <th>Add Qty</th>

                            <th>Update at</th>
                            @if ($user_type == 'admin')
                                <th>Action</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($current_stock as $item)
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->article_no }}</td>
                                <td class="total_stock">{{ formatQtyPrice($item->stock) }}</td>
                                <td style="width:10%">
                                    <input type="number" class="add_qty form-control" min="0" steps="0.00"
                                        data-product-id="{{ $item->product_id }}" data-id="{{ $item->id }}"  name="updateStock[{{ $item->id ?? 'new' }}][{{$item->id ?? $item->product_id}}]">
                                </td>


                                <td>{{ $item->updated_at }}</td>
                                @if ($user_type == 'admin')
                                    <td>
                                        <button class="updateStockBtn btn btn-primary  btn-sm"
                                            data-product-id="{{ $item->product_id }}"
                                            data-id="{{ $item->id }}" type="button">Update</button>
                                        <button class="btn btn-secondary btn-sm view" type="button"
                                            value="{{ $item->id }}"><i class="fa fa-history"
                                                aria-hidden="true"></i></button>
                                    </td>
                                @endif

                            </tr>
                        @endforeach

                    </tbody>

                </table>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary " type="submit">Update Stock</button>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div>
                {{ $current_stock->appends(['search' => request('search')])->links() }}
            </div>
        </div>

    </div>


    <form action="{{ route('SaveStock') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Stock Adjustment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="product_id" name="product_id">
                        <label>Qty</label>
                        <input type="number" step="0.01" class="form-control" name="qty" required>

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
    <div class="modal fade" id="viewModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Stock Adjustment History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Qty</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody id="viewList">

                        </tbody>

                    </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).val())
            $("#product_id").val($(this).data("product_id"))

            $("#modalId").modal("show")
        });

        $(document).on("click", ".view", function() {
            var id = $(this).val();

            $.ajax({
                url: "/GetStockAdjustmentHistory",
                type: "POST",
                data: {
                    id: id,
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
                                    <td>${element.qty}</td>    
                                    <td>${element.created_at}</td>    
                                </tr>
                            `;

                    });

                    $("#viewList").html(html)
                    $("#viewModal").modal("show")

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });


        });


        $(document).on("click", ".updateStockBtn", function() {
            let productId = $(this).data('product-id');
            let id = $(this).data('id');
            let input = $('.add_qty[data-id="' + id + '"]');
            let addQty = input.val();

            if (!addQty || isNaN(addQty) || parseFloat(addQty) <= 0) {
                alert('Please enter a valid quantity');
                return;
            }

            $.ajax({
                url: '{{ route('SaveStock') }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: productId,
                    id: id,
                    qty: addQty
                },
                success: function(response) {
                    input.closest('tr').find('.total_stock').text(response.total_stock);
                    input.val('');
                    toastr.success('Stock Updated');
                },
                error: function(xhr) {
                    let res = xhr.responseJSON;
                    if (res && res.error) {
                        toastr.error(res.error);
                    } else {
                        toastr.error('Something went wrong');
                    }
                    input.val('');
                }
            });
        });
    </script>
@endsection
