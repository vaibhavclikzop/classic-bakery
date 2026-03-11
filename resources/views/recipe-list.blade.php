@extends('layouts.main')
@section('main-section')
<style>
     .wrap-text{
             word-wrap: break-word!important; /* older browsers */
        overflow-wrap: break-word!important; /* modern browsers */
        white-space: normal!important; /* allow line breaks */
        max-width: 200px!important;
        }
</style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> Recipe for Production </h4>
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
                        <th>Department</th>
                        <th>Description</th>
                        <th>Batch</th>
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
                            <td>{{ $item->dname }}</td>
                             <td class="wrap-text" style="width: 20%">{{ $item->description }}</td>
                            <td>{{ $item->batch }}</td>
                            
                            <td>
                                {{-- <a href="/recipe-view/{{ $item->id }}" class="btn btn-primary btn-sm"> <i class="fa fa-eye"
                                        aria-hidden="true"></i> </a> --}}
                                        <a href="/make-recipe/{{$item->id}}" class="btn btn-dark btn-sm"> Make Recipe </a>
                                
                                 <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteReceipe('{{ $item->id }}')"> <i class="fa fa-trash"
                                        aria-hidden="true"></i> </a>        
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
         function deleteReceipe(id) {
        Swal.fire({
            title: "Are you sure?",
            text: `Are you sure you want to delete this?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete it!",
            cancelButtonText: "No",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                fetch("{{ route('delete-recipe') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            id: id,
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                         if (data.success) {
                            Swal.fire("Updated!", data.message, "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Error!", data.message, "error");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        
                    });
            }
        });
    }
    </script>
@endsection
