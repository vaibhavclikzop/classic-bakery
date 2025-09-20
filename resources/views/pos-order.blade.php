@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Invoices</title>
    @endpush


    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Invoices</h4>
            </div>
            <div class="">

      




            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Outlet </th>
                        <th> Invoice No</th>
                        <th> Customer Name</th>
                        <th> Customer Number</th>
                        <th> Sub Total</th>
                        <th> Discount</th>
                        <th> Total</th>
                        <th>
                            Action
                        </th>
              




                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->outletDetails->outlet_name }}</td>
                            <td><a href="/pos-order-view/{{$item->id}}" class="btn btn-sm btn-outline-primary"> {{ $item->invoice_no }}</a></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->sub_total }}</td>
                            <td>{{ $item->discount_percentage }}</td>
                            <td>{{ $item->total }}</td>
                              <td>
                                <button class="btn btn-danger btn-sm delete" data-id="{{ $item->id }}"><i
                                        class="fa fa-trash" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

        <form action="" method="POST">
        @csrf
        <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Remove Kot
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        Are you sure you want to remove?

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

    <form action="{{ route('delete_kot') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="deleteKotForm">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content p-5 px-3 text-center">
                            <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i
                                    class="fa fa-trash fs-24 text-danger"></i></span>
                            <h4 class="fs-20 text-gray-9 fw-bold mb-2 mt-1">Delete </h4>
                            <input type="hidden" id="deleteId" name="id">
                            <p class="text-gray-6 mb-0 fs-16">Enter password to delete?</p>
                            <div class="pass-group" style="position: relative;max-width: 300px; margin: 0 auto;">
                                <input type="password" class="pass-input form-control" value="" name="order_pwd"
                                    required>

                            </div>


                            <div class="modal-footer-btn mt-3 d-flex justify-content-center">
                                <button type="button" class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("click", ".delete", function() {
            $("#deleteId").val($(this).data("id"))
            $("#deleteKotForm").modal("show")
        })
    </script>
@endsection
