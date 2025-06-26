@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Attendance Report</h4>
            </div>
            <form action="{{ route('attendance-report-monthly') }}" method="GET" class="needs-validation" novalidate>
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
                    
                    <div class="col-md">
                        <button class="btn btn-info" type="submit">Search </button>

                    </div>

                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th>S.no</th>
                        <th>Users</th>
                        <?php
                        if (!empty($sdate1)) {
                            while (strtotime($sdate1)  < strtotime($edate1)) {
                                if (date('D', strtotime($sdate1)) == 'Sun') {
                                    echo '<th style="background-color:red;color:white">' . date('d', strtotime($sdate1)) . ' ' . date('D', strtotime($sdate1)) . '</th>';
                                } else {
                                    echo '<th>' . date('d', strtotime($sdate1)) . ' ' . date('D', strtotime($sdate1)) . '</th>';
                                }
                                $sdate1 = date('Y-m-d', strtotime($sdate1 . ' +1 day'));
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($attendance_monthly)) {
                        foreach ($attendance_monthly as $key => $row) {
                            echo '<tr>';
                            echo '<td>' . ($key + 1) . '</td>';
                            foreach ($row as $k => $v) {
                                if ($k == 0) {
                                    echo '<td >' . $v['col'] . '</td>';
                                } else {
                                    if ($v['hours'] == 0)
                                        echo '<td style="background-color:red;color:white">A</td>';
                                    else if ($v['hours'] == 1)
                                        echo '<td style="background-color:#ff9d00;color:white">' . $v['hours'] . '</td>';
                                    else
                                        echo '<td style="background-color:green;color:white">P</td>';
                                }
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>


            </table>
        </div>

    </div>
@endsection
