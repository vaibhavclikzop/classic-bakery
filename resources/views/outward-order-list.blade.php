@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Outward order list </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Outward order list</h4>
            </div>
          

        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Department </th>
                    
                    
                        <th>Invoice Date </th>
                 
                        <th>User </th>
                        <th>Action </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($outward as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                         
                    
                            <td>{{ $item->department }}</td>
                            <td>{{ $item->invoice_date }}</td>
                 
                            <td>{{ $item->user }}</td>
                            <td > 

                                <a class="btn btn-primary btn-sm" href="/outward-challan-view/{{$item->id}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                          
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
 
@endsection
