@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Settings</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Settings</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('SaveSettings') }}" class="row needs-validation" novalidate
                enctype="multipart/form-data">
                @csrf
                <div class="col-md-12 mb-3">
                    <img src="/logo/{{ $settings->img }}" alt="" width="280px">

                </div>
                <div class="col-md-3">

                    <label for="">Image</label>
                    <input type="file" name="image" class="form-control">

                </div>
                <div class="col-md-3">
                    <label for="">Image Width (in Pixels)</label>
                    <input type="number" name="img_width" class="form-control" value="{{ $settings->img_width }}">

                </div>
                <div class="col-md-3">
                    <label for="">Company Name</label>
                    <input type="text" class="form-control" name="company_name" value="{{ $settings->company_name }}">

                </div>
                <div class="col-md-3">
                    <label for="">Contact Person Name</label>
                    <input type="text" class="form-control" name="contact_person"
                        value="{{ $settings->contact_person }}">

                </div>
                <div class="col-md-12 mt-2">
                    <label for="">Address</label>
                    <textarea name="address" id="" class="form-control">{{ $settings->address }}</textarea>

                </div>

                <div class="col-md-4 mt-2">
                    <label for="">Number</label>
                    <input type="number" class="form-control" name="number" value="{{ $settings->number }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $settings->email }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">GST</label>
                    <input type="text" class="form-control" name="gst_no" value="{{ $settings->gst_no }}">

                </div>

                <div class="col-md-4 mt-2">
                    <label for="">FSSAI No.</label>
                    <input type="text" class="form-control" name="fssai_no" value="{{ $settings->fssai_no }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">CIN No.</label>
                    <input type="text" class="form-control" name="cin_no" value="{{ $settings->cin_no }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">PAN No.</label>
                    <input type="text" class="form-control" name="pan_no" value="{{ $settings->pan_no }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">City</label>
                    <select name="city" id="city" class="form-control" required>
                        <option value="">Select</option>
                        @foreach ($city as $item)
                            <option value="{{ $item->city }}" {{ $settings->city == $item->city ? 'selected' : '' }}>
                                {{ $item->city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mt-2">
                    <label for="">PO Invoice Prefix</label>
                    <input type="text" class="form-control" name="invoice_prefix"
                        value="{{ $settings->invoice_prefix }}">

                </div>
                {{-- <div class="col-md-4 mt-2 d-none">
                    <label for="">PO Invoice No</label>
                    <input type="number" class="form-control" name="invoice_no" value="{{ $settings->invoice_no }}">

                </div> --}}


                <div class="col-md-4 mt-2">
                    <label for="">Order Invoice Prefix</label>
                    <input type="text" class="form-control" name="order_prefix" value="{{ $settings->order_prefix }}">

                </div>
                <div class="col-md-4 mt-2">
                    <label for="">Advance Order Prefix</label>
                    <input type="text" class="form-control" name="adv_order_prefix"
                        value="{{ $settings->adv_order_prefix }}">

                </div>

                <div class="col-md-4 mt-2">
                    <label for="">Generate PO Prefix</label>
                    <input type="text" class="form-control" name="po_prefix" value="{{ $settings->po_prefix }}">

                </div>

                <div class="col-md-4 mt-2">
                    <label for="">Create Order Prefix</label>
                    <input type="text" class="form-control" name="create_order_prefix"
                        value="{{ $settings->create_order_prefix }}">

                </div>

                <div class="col-md-4 mt-2">
                    <label for="">Outward Production Order Prefix</label>
                    <input type="text" class="form-control" name="outward_production_prefix"
                        value="{{ $settings->outward_production_prefix }}">

                </div>
                {{-- <div class="col-md-4 mt-2 d-none">
                    <label for="">Order Invoice No</label>
                    <input type="number" class="form-control" name="order_no" value="{{ $settings->order_no }}">

                </div> --}}
                <div class="col-md-4 mt-2">
                    <label for="">Order Password</label>
                    <div class="pass-group">
                        <input type="password" class="pass-input form-control" value="{{ $settings->order_pwd }}"
                            name="order_pwd">
                        <i class="fa toggle-password fa-eye "></i>
                    </div>

                </div>


                <div class="col-md-12 text-center mt-4">
                    <button class="btn btn-primary" type="submit">Update</button>

                </div>

            </form>
            <hr>

        </div>

    </div>


    <form action="{{ route('SaveHeaderMenu') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Header Menu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hid" name="id">
                        <label for="">Name</label>
                        <input type="text" name="name" id="name" class="form-control">

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
            $("#city").select2();
        })
        $(document).on("click", ".edit", function() {
            $("#name").val($(this).data("name"))
            $("#seq").val($(this).data("seq"))
            $("#hid").val($(this).val())
            $("#modalId").modal("show")
        });
    </script>
@endsection
