@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Finish Goods Defective Stock</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Finish Goods Defective Stock</h4>
            </div>
            <div class="">


         

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Qty</th>


                  

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td>{{ $item->product }}</td>
                            <td>{{ $item->qty }}</td>



                           

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>


 
@endsection
