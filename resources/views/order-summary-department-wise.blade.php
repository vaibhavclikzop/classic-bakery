@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Summary Department Wise</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Summary Department Wise</h4>
            </div>
            <div class="d-flex">



                <form method="get" class="mx-4 d-flex">
                    <input type="hidden" name="id" value="{{ request('id') }}">
                    <a class="btn btn-info" 
                    href="/order-summary-department-wise?id={{ request("id") }}&date={{ date('Y-m-d', strtotime(request('date') . ' -1 day')) }}">
                    <<
                 </a>
                    <input type="date" name="date" onchange="this.form.submit()" value="{{ request('date') }}"
                        class="form-control mx-2">

                        <a class="btn btn-info" 
                        href="/order-summary-department-wise?id={{ request("id") }}&date={{ date('Y-m-d', strtotime(request('date') . ' +1 day')) }}">
                        >>
                     </a>

                </form>
                <button type="button" onclick="printcontent()" class="btn btn-primary mx-2"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
                        <a class="btn btn-dark mx-2" href="/order-summary-customer-wise"  >Order Wise</a>
            </div>
        </div>
        <div class="card-body">
            <div>
                @foreach ($department as $item)
                    <a class="btn btn-sm {{ request('id') == $item->id ? 'btn-success' : 'btn-dark' }} "
                        href="/order-summary-department-wise?id={{ $item->id }}&date={{ request('date') }}">{{ $item->name }}</a>
                @endforeach
            </div>
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
                            @if ($department_details)
                                
                        
                            <h4>{{ $department_details->name }}</h4>
                            <p>
                                {{ $department_details->contact_person }} <br>
                                {{ $department_details->number }} <br>
                                {{ $department_details->delivery_date }} <br>




                            </p>
                            @else
                            <h4>No Order Found</h4>
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
                            <th>Product</th>
                        
                            <th>Qty</th>
                          


                        </thead>
                        <tbody>


                            @foreach ($work_order_det as $item)
                                <tr>
                                    <td>{{$sno++}}</td>
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
