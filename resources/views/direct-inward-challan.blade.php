@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Direct Inward Challan</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Direct Inward Challan</h4>
            </div>
            <div class="d-flex">



                <form method="get" class="mx-4 d-flex">
                    <input type="hidden" name="id" value="{{ request('id') }}">
                    <a class="btn btn-info" 
                    href="/direct-inward-challan?id={{ request("id") }}&date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                    <<
                 </a>
                    <input type="date" name="date" onchange="this.form.submit()" value="{{ request('date') }}"
                        class="form-control mx-2">

                        <a class="btn btn-info" 
                        href="/direct-inward-challan?id={{ request("id") }}&date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                        >>
                     </a>

                </form>
                <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
                        <a class="btn btn-dark mx-2" href="/order-summary-customer-wise"  >Order Wise</a>
            </div>
        </div>
        <div class="card-body">
           
            <div id="PrintOrder" class="mt-5">

                <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                    <div>
                        <h3>{{ $setting->company_name }}</h3>
                        <p>{!! $setting->address !!}
                            <br>
                            E-Mail : {{ $setting->email }} <br>
                            Phone : {{ $setting->number }} <br>
                            GST : {{ $setting->gst_no }}

                        </p>


                    </div>


                    <div>
                        <div style="text-align: right;">
                            @if ($direct_inward_mst)
                                
                        
                            <h4>{{ $direct_inward_mst->date }}</h4>
                          




                            </p>
                            @else
                            <h4>No Challan Found</h4>
                            @endif
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
                            <th>Sub Category</th>
                            <th>Product</th>
                        
                            <th>Qty</th>
                          


                        </thead>
                        <tbody>


                            @foreach ($direct_inward_det as $item)
                                <tr>
                                    <td>{{$sno++}}</td>
                                    <td>{{$item->sub_category}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->qty}}</td>
                                </tr>
                            @endforeach
                        </tbody>

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

    </div>
@endsection
