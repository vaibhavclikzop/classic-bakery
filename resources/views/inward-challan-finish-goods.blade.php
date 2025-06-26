@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Inward Challan Finish Goods</h4>
            </div>
            <div class="">
                <form action="" class="d-flex">
                    <div>
                        <input type="date" name="fromDt" class="form-control" onchange="this.form.submit()" value="{{request("fromDt")}}">
                    </div>
                     <div>
                        <input type="date" name="toDt" class="form-control" onchange="this.form.submit()" value="{{request("toDt")}}">
                    </div>
                </form>
            </div>
            <div>

            </div>
        </div>
        <div class="card-body" id="">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vendor</th>
                        <th>PO</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>RM Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno=1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{$sno++}}</td>
                            <td>{{$item->vendorDetails->name}}</td>
                            <td>{{$item->poDetails->po_id}}</td>
                            <td>{{$item->invoice_no}}</td>
                            <td>{{$item->invoice_date}}</td>
                            <td>{{$item->received_material_date}}</td>
                            <td>
                                <a href="/inward-challan-finish-goods-view/{{$item->id}}" class="btn btn-primary btn-sm"> <i class="fa fa-eye" aria-hidden="true"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

    </div>
  
@endsection
