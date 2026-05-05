@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Department Sale Report</h4>
        </div>

        <div>
            <div>
                <form action="" class="d-flex">
                    <div>
                        <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                    </div>
                    <div class="mx-1">
                        <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                    </div>

                    <div>
                        <select name="department_id" id="department_id" class="form-control">
                            <option value="">Department</option>
                            @foreach ($department as $item)
                            <option value="{{ $item->id }}"
                                {{ request('department_id') == $item->id ? 'selected' : '' }}>
                                 {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mx-1">
                        <Button class="btn btn-primary" type="submit">Search</Button>
                    </div>
                    <div>
                        <div>
                            <button id="exportToExcel" data-name="Department Consumption Report"
                                class="btn btn-success float-end btn-sm mx-2">Export
                                to Excel</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="card-body">
        <table class="table" id="exportTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Sub Category</th>
                    <th>Qty</th>
                    <th>Sale amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($data as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->sub_category }}</td>
                    <td>{{formatQtyPrice($item->qty)}}</td>
                    <td>{{formatQtyPrice($item->sale_amount)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#category_id").select2();
        $("#departName").text($("#department_id").find(":selected").text())
    })
</script>
@endsection