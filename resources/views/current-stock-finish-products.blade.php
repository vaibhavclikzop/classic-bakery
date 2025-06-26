@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4">



                </div>
            </form>
        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product Name</th>


                        <th>Stock</th>
                        <th>Update at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($current_stock as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->product }}</td>


                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit" type="button" value="{{ $item->id }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>

                                <button class="btn btn-secondary btn-sm view" type="button" value="{{ $item->id }}"><i
                                        class="fa fa-history" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>


    <form action="{{ route('SaveFPStock') }}" method="POST" class="needs-validation" novalidate>
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
                        <label>Qty</label>
                        <input type="number" class="form-control" name="qty" required>

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
            $("#modalId").modal("show")
        });

        $(document).on("click", ".view", function() {
            var id = $(this).val();

            $.ajax({
                url: "/GetFPStockAdjustmentHistory",
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
    </script>
@endsection
