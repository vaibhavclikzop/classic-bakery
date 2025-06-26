@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Attendance Report</h4>
            </div>
            <form action="{{ route('attendance-report') }}" method="GET" class="needs-validation" novalidate>
                <div class="row mt-4">
                    <div class="col-md-3 text center">
                        <select name="year" id="year" class="form-control" required>
                            <option value="">--Select Year--</option>
                            <?php
                            for ($i = date('Y'); $i > 2023; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 text center">
                        <select name="month" id="month" class="form-control" required>
                            <option value="">--Select Month--</option>
                            <option value="1">Jan</option>
                            <option value="2">Feb</option>
                            <option value="3">Mar</option>
                            <option value="4">Apr</option>
                            <option value="5">May</option>
                            <option value="6">Jun</option>
                            <option value="7">Jul</option>
                            <option value="8">Aug</option>
                            <option value="9">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                    </div>
                    <div class="col-md-3 text center">
                        <select name="emp_id" id="emp_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md">
                        <button class="btn btn-info" type="submit">Search </button>

                    </div>

                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead class="bg-info">
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Working Hours</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$attendance_report) {
                        echo '<tr><td colspan=4>there is no record.</td></tr>';
                    } else {
                        foreach ($attendance_report as $key => $value) {
                           
                            echo '<tr>';
                            echo '<td>' . date('d-m-y', strtotime($value->start_time)) . '</td>';
                            echo '<td>' . date('h:i:s a', strtotime($value->start_time)) . '</td>';
                            if (!empty($value->end_time)) {
                                $d1 = new DateTime($value->start_time);
                                $d2 = new DateTime($value->end_time);
                                $diff = $d1->diff($d2);
                    
                                echo '<td>' . date('h:i:s a', strtotime($value->end_time)) . '</td>';
                            } else {
                                echo '<td>Not Ended</td>';
                            }
                    
                            if (empty($value->end_time)) {
                                echo '<td>0 Hour</td>';
                            } else {
                                echo '<td>' . $diff->format('%h') . ' Hours ' . $diff->format('%i') . '  Hours ' . $diff->format('%s') . ' Seconds</td>';
                            }
                    
                            // echo '<td><img src="' . $row['start_selfie'] . '" /> <img src="' . $row['end_selfie'] . '" /> </td>';
                          
                            
                    
                            echo '</tr>';
                        };
                    }
                    
                    ?>
                </tbody>


            </table>
        </div>

    </div>
@endsection
