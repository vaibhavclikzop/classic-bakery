@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order View</h4>
            </div>
            <div class="">

                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
                {{-- @if (request('edit') == 1)
                    <a class="btn btn-dark" href="?edit=0"><i class="fa fa-eye" aria-hidden="true"></i></a>
                @else
                    <a class="btn btn-dark" href="?edit=1"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                @endif --}}

            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="text-center">
                <img src="/logo/{{ $setting->img }}" width="180px">
            </div>

            <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                <div>
                    <h3>{{ $setting->company_name }}</h3>
                    <p>{!! $setting->address !!}
                        <br>
                        E-Mail : {{ $setting->email }} <br>
                        Phone : {{ $setting->number }} <br>
                        GST : {{ $setting->gst_no }} <br>
                        PO ID : {{ $po_mst->po_id }}
                    </p>


                </div>


                <div>
                    <div style="text-align: right;">
                        <h4>{{ $po_mst->company_name }}</h4>
                        <p>
                            {{ $po_mst->vendor_name }} <br>
                            {{ $po_mst->vendor_address }},<br> {{ $po_mst->vendor_state }}, {{ $po_mst->vendor_city }}, ,
                            {{ $po_mst->vendor_pincode }} <br>
                            {{ $po_mst->vendor_number }} <br>
                            {{ $po_mst->vendor_email }} <br>
                            {{ $po_mst->vendor_gst }} <br>

                        </p>

                    </div>
                </div>
            </div>
            <div class="">
                <hr>
                <h6>Products</h6>
                @php
                    $sno = 1;
                @endphp
                <table class="table">
                    <thead>
                        <th>S.No</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>CGST/SGST</th>
                        <th>IGST</th>
                        <th>CESS</th>
                        <th>Total</th>
                        @if (request('edit') == 1)
                            <td> <button class="btn btn-success add" type="button"> Add </button></td>
                        @endif
                    </thead>
                    <tbody>
                        @php
                            $total_gst = 0;
                            $sub_total = 0;
                            $total_cess = 0;
                        @endphp
                        @foreach ($po_det as $item)
                            @php
                                $total_gst += (($item->price * $item->qty) / 100) * $item->gst;
                                $total_cess += (($item->price * $item->qty) / 100) * $item->cess_tax;
                                $sub_total += $item->price * $item->qty;
                            @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->sub_category }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->price }}</td>
                                @if ($item->gst_type == 'Inner GST')
                                    <td>{{ $item->gst }}</td>
                                @else
                                    <td>0</td>
                                @endif

                                @if ($item->gst_type == 'Outer GST')
                                    <td>{{ $item->gst }}</td>
                                @else
                                    <td>0</td>
                                @endif
                                <td>{{ $item->cess_tax }}</td>

                                <td>{{ $item->price * $item->qty + (($item->price * $item->qty) / 100) * $item->gst }}</td>
                                @if (request('edit') == 1)
                                    <td>
                                        <button class="btn btn-danger delete" value="{{ $item->id }}" type="button"><i
                                                class="fa fa-trash" aria-hidden="true"></i></button>
                                    </td>
                                @endif


                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">

                            </th>
                            <th>Subtotal</th>
                            <th>{{ $sub_total }}</th>
                        </tr>
                        <tr>
                            <th colspan="7">

                            </th>
                            <th>GST</th>
                            <th>{{ $total_gst }}</th>
                        </tr>
                          <tr>
                            <th colspan="7">

                            </th>
                            <th>Cess Tax</th>
                            <th>{{ $total_cess }}</th>
                        </tr>
                        <tr>
                            <th colspan="7">

                            </th>
                            <th>Grand Total</th>
                            <th>{{ $total_gst + $sub_total+ $total_cess  }}</th>
                        </tr>

                    </tfoot>

                </table>
            </div>
            <div class="d-flex mt-4 justify-content-between">
                <div>
                    <p><b><u><i>Terms & Conditions</i></u></b></p>
                    <ol style="list-style:number;">


                    </ol>
                </div>
                <div>
                    <h6 class="float-end">For {{ $setting->company_name }}</h6>

                    <p class="mt-5">Authorized Signatory</p>
                </div>

            </div>




        </div>

    </div>


    <form action="{{ route('DeletePOProduct') }}" method="POST">
        @csrf
        <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="did" name="id">
                        <h4>Are you sure you want to delete this entry?</h4>
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


    <form action="{{ route('AddPOProduct') }}" method="POST">
        @csrf
        <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Add Product
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <input type="hidden" name="mst_id" value="{{ $po_mst->id }}">
                            <div class="col-md-6">
                                <label for="">
                                    Product
                                </label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label for="">Qty</label>
                                <input type="number" step="0.01" name="qty" class="form-control">

                            </div>

                        </div>
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
        $(document).ready(function() {
            $("#product_id").select2();
        })
        $(document).on("click", ".delete", function() {
            $("#did").val($(this).val())
            $("#deleteModal").modal("show");
        })
        $(document).on("click", ".add", function() {
            $("#aid").val($(this).val())
            $("#addModal").modal("show");
        })
    </script>
@endsection
