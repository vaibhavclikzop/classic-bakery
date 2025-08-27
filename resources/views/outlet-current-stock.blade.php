@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Outlet Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4 col-4">

                    <select name="outlet_id" id="" required class="form-control" onchange="this.form.submit()">
                        <option value="">Select Outlet</option>
                        @foreach ($outlet as $item)
                            <option value="{{ $item->id }}" {{ request('outlet_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->outlet_name }}</option>
                        @endforeach
                    </select>

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
                        <th>Product Name</th>


                        <th>Stock</th>
                        <th>Update at</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno=1;
                    @endphp
                   
                    @foreach ($current_stock as $item)
                     <tr>
                        <td>{{$sno++}}</td>
                        <td>{{$item->product}}</td>
                        <td>{{$item->stock}}</td>
                        <td>{{$item->updated_at}}</td>
                     </tr>
                    @endforeach


                </tbody>

            </table>
        </div>

    </div>
@endsection
