@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Inward Stock</h4>
            </div>
            <div class="">

            </div>
        </div>
        <div class="card-body" id="">
            <form method="POST" action="{{ route('SaveInwardStockFinishGoods') }}" id="formMain">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <label for="">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} / {{ $item->company_name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-3">
                        <label for="">PO</label>
                        <select name="po_id[]" id="po_id" class="form-control" multiple>
                            <option value="">Select</option>

                        </select>

                    </div>


                    <div class="col-md-3">
                        <label>Invoice No</label>
                        <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                            placeholder="Enter Invoice No.">

                    </div>

                    <div class="col-md-3">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mt-3">
                        <label>Received Material Date</label>
                        <input type="date" name="received_material_date" id="received_material_date" class="form-control"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Enter Description">
                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>

                                <tr>
                                    <th>S.No</th>
                                    <th>Product Name</th>
                                    <th>Article Code</th>
                                    <th>Acutal Qty</th>
                                    <th>Received Qty</th>
                                    <th>Inward Qty</th>
                                    <th>Price</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productList">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">Total </th>
                                    <th id="subtotal"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <input type="hidden" id="prod_list" name="prod_list">
                        </table>

                    </div>

                </div>

                <div class="text-center col-md-12 mt-3">

                    <button type="button" id="Save" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>


        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#vendor_id").select2();
            $("#po_id").select2();

            function getRandomColor() {
                let letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            var product_list = [];

            $("#vendor_id").on("change", function() {
                $.ajax({
                    url: "/GetPOFinishGoods",
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
                        html += '<option value="">----Select PO----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '">' + element
                                .name + ' /   ' + element
                                .po_id +
                                '</option>';
                        });
                        $("#po_id").html(html)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });


            $("#po_id").on("change", function() {
                $.ajax({
                    url: "/GetPODetFinishGoods",
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

                        product_list = [];
                        result.forEach(element => {
                            var product_id = element.product_id
                            var qty = element.qty
                            var price = element.price
                            var r_qty = parseFloat(qty - element.received_qty)
                            var tableHead = "";
                            var id = 0;
                            var gst = element.gst;
                            var po_id = element.mst_id;
                            var cess_tax = element.cess_tax;
                            var po_det_id = element.id;


                            sno = 1;
                            if (r_qty > 0) {


                                html += `
                                        <tr class="product${product_id}">
                                        <td>${sno++}</td>    
                                        <td>${element.product_name}</td>    
                                        <td>${element.article_no}</td>    
                                        <td>${element.qty}</td>    
                                        <td>${element.received_qty}</td>    
                                        <td>
                                            <input type="number" step="" class="form-control qty"  data-product_id="${product_id}"  value="${r_qty}" data-received_qty="${element.received_qty}" data-actual_qty="${element.qty}">
                                            </td>    
                                       
                                        <td><input type="number" step="0.01" class="form-control price"  data-id="${product_id}"   value="${element.price}"></td>    

                                        
                                        <td><button class="btn btn-sm btn-danger remove" type="button" data-id="${product_id}" ><i class="fa fa-trash" aria-hidden="true"></i></button></td>    
                                        </tr>
                                    `;

                                qty = r_qty;

                                product_list.push({
                                    product_id,
                                    qty,
                                    price,
                                    gst,
                                    po_id,
                                    cess_tax,
                                    po_det_id
                                });

                            }

                        });

                        console.log(product_list);
                        calculate_total(product_list)
                        $("#productList").html(html)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.product_id !== id);
                calculate_total(product_list)

            });

            $("#Save").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));
                if (!$("#vendor_id").val()) {
                    toastr.error("Select Vendor");
                    return;
                }

                if (!$("#po_id").val()) {
                    toastr.error("Select po");
                    return;
                }

                if (!$("#invoice_no").val()) {
                    toastr.error("Enter Invoice No.");
                    return;
                }


                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }


                $("#formMain").submit();
            })
            $(document).on("keyup", '.qty', function() {
                var product_id = parseInt($(this).data("product_id"))

                var qty = parseFloat($(this).val());
                var received_qty = parseFloat($(this).data("received_qty"))
                var actual_qty = parseFloat($(this).data("actual_qty"))
                var remaining_qty = actual_qty - received_qty;

                if (qty <= 0) {
                    toastr.error("Qty can not be zero or less then zero");
                    $(this).val(remaining_qty)
                    return;
                }

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {

                    product.qty = qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
                calculate_total(product_list)


            })
            $(document).on("keyup", '.price', function() {
                var id = parseInt($(this).data("id"))

                var price = parseFloat($(this).val());
                var product = product_list.find(item => item.product_id === id);

                if (product) {

                    product.price = price;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
                calculate_total(product_list)

            });

            function calculate_total(product_list) {
                let total = 0;

                product_list.forEach(item => {
                    const base_total = item.qty * item.price;

                    total += base_total;
                });

                $("#subtotal").text(parseFloat(total).toFixed(2));


            }



        });
    </script>
@endsection
