@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4">
                    <div>

                        

                    </div>
                    

                </div>
            </form>
        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Sub Category</th>
                        <th>Product Name</th>
                        <th>Article No</th>
            
                        <th>Min Stock</th>
                        <th>Stock</th>
                        <th>Update at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($current_stock as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->sub_category }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->article_no }}</td>
                       
                
                            <td>{{ $item->min_stock }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->updated_at }}</td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>
@endsection
