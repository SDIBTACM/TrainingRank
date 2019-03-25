@extends('layout')

@section('main')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <strong>Introduction: </strong> 手动录入每次比赛排名，通过 Codeforce 开源的 Rating 算法产生一个rating。。

            </div>

            <div class="card-body form-inline">
                <div id="studentRatingRankTable" class="col-lg-6">
                    <h3 class="h3"> Latest Rating (Random </h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col"> # </th>
                                <th scope="col"> Name </th>
                                <th scope="col"> Student Id </th>
                                <th scope="col"> Rating </th>
                                <th scope="col"> ...</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php $i = 1;?>
                        @foreach($students as $student)
                            <tr>
                                <th scope="row">{{ $i++ }}</th>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->rating }}</td>
                                <td><a href="{{ route('student.show', ['student_id' => $student->id]) }}">Detail</a></td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>

                <div id="ContestTable" class="col-lg-6">
                    <h3 class="h3"> Recently Contest </h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col"> # </th>
                            <th scope="col"> Name </th>
                            <th scope="col"> Date </th>
                            <th scope="col"> ...</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $i = 1;?>
                        @foreach($contests as $contest)
                            <tr>
                                <th scope="row">{{ $i++ }}</th>
                                <td>{{ $contest->name }}</td>
                                <td>{{ $contest->start_time }}</td>
                                <td><a href="{{ route('contest.show', ['contest_id' => $contest->id]) }}">Detail</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

@endsection