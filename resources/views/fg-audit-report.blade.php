@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Audit Report Finish Goods</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Audit Report Finish Goods</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
           <table class="table dataTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sno=1;
                @endphp
                @foreach ($audit_report_mst as $item)
                    <tr>
                        <td>{{$sno++}}</td>
                        <td>{{$item->date}}</td>
                        <td>{{$item->category}}</td>
                        <td>{{$item->remarks}}</td>
                        <td>{{$item->status}}</td>
                        <td>{{$item->name}}</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="/fg-audit-report-view/{{$item->id}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    
                @endforeach

            </tbody>

           </table>
        </div>

    </div>

 
@endsection
