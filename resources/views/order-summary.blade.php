@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Summary</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Summary</h4>
            </div>
            <div class="">

                <form method="GET" action="{{ route('order-summary') }}" class="needs-validation d-flex" novalidate>
                    <div>


                        <input type="date" name="date" class="form-control" value="{{ request('date') }}" required>
                    </div>
                    <div>
                        <button class="btn btn-primary mx-1">Orders</button>
                    </div>


                </form>



            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('GenerateWorkOrder') }}">
                <input type="hidden" name="date" value="{{request("date")}}">
                @csrf
                <button class="btn btn-dark float-end">Proceed Order</button>
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checks" checked> </th>
                            <th>S.no</th>
                            <th> Order ID</th>
                            <th> Customer Name</th>


                            <th>Order Date</th>
                            <th>Delivery Date</th>
                            <th>Description</th>

                            <th>User</th>

                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sno = 1;
                        @endphp
                        @foreach ($orders as $item)
                            <tr>
                                <th>
                                    <input type="checkbox" checked name="order_ids[]" value="{{$item->id}}" class="checks">
                                </th>
                                <th>{{ $sno++ }}</th>
                                <th>{{ $item->order_id }}</th>
                                <th>{{ $item->customer }}</th>


                                <th>{{ $item->order_date }}</th>
                                <th>{{ $item->delivery_date }}</th>
                                <th>{{ $item->description }}</th>

                                <th>{{ $item->user }}</th>

                                <th>



                                    {{-- @if ($item->status != 'complete')
                                     <a class="btn btn-sm btn-info" href="/outward-order">Outward</a>
                                @endif --}}


                                    <a href="/order-view/{{ $item->id }}" class="btn btn-secondary btn-sm"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                </th>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </form>
        </div>

    </div>

 <script>
    $("#checks").on("click",function(){
        if($(this).prop("checked")){
            $(".checks").prop("checked",true)
        }else{
            $(".checks").prop("checked",false)
        }
    })
 </script>

 

  

 
@endsection
