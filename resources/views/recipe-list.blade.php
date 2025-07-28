@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Recipe List </h4>


            </div>
            <div class="">

                {{-- <a href="generate-po-product" class="btn btn-dark">Generate PO Via Products</a> --}}

            </div>

        </div>
        <div class="card-body">

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>

                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>

                            <td>{{ $item->description }}</td>
                            <td>
                                <a href="/recipe-view/{{ $item->id }}" class="btn btn-primary btn-sm"> <i class="fa fa-eye"
                                        aria-hidden="true"></i> </a>
                                        <a href="/make-recipe/{{$item->id}}" class="btn btn-dark btn-sm"> Make Recipe </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
@endsection
