@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Outlet Product</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">

                <h4 class="">Outlet  product list <br> </h4>
                <span  > Name : {{$outlet->outlet_name}}  </span> <br>
  
      

            </div>
            <div class="">


       

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                    
                  
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Sale Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($outlet_product as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                 
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->sale_price }}</td>

                        
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>
@endsection
