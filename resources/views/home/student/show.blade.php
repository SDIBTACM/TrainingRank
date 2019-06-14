<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-22 21:44
 */
?>

@extends('layout')

@section('main')
    <div class="container">
        <div >
            <h2 class="title">{{ __($title) }}</h2>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-subtitle"> {{ __('Student Name') }}: {{ $student->name }}</h4>
                <p class="card-subtitle"> {{ __('Student ID') }}: {{ $student->student_id }} </p>

                <form>
                    <div class="form-inline">
                        <div class="form-group mb-2 mx-sm-3">
                            <label for="groupSelect" class="sr-only"></label>
                            <select id="groupSelect" class="form-control" name="type" onchange="$('#SubmitButton').click();">
                                <option value="cf_rating" {{ Request::get('type', 'cf_rating') == 'cf_rating' ? 'selected': '' }}> CF Rating </option>
                                <option value="solved" {{ Request::get('type', 'cf_rating') == 'solved' ? 'selected': '' }}> Solved Count </option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <input type="submit" class="form-control btn btn-primary" id="SubmitButton" value="Get It">
                        </div>
                    </div>

                    <div class="form-group form-inline">
                        <label for="startSelect"  class="col-sm-1 col-form-label">{{__("Start at")}}</label>
                        <select id="startSelect" name="start_at" class="form-control">
                            <option> Please Select... </option>
                            @foreach($contests as $contest)
                                @if (Request::get('start_at') == $contest->id)
                                    <option value="{{ $contest->id }}" selected> {{ $contest->name }} </option>
                                @else
                                    <option value="{{ $contest->id }}" > {{ $contest->name }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-inline">
                        <label for="endSelect" class="col-sm-1 col-form-label" >{{__('End before')}}</label>
                        <select id="endSelect" name="end_at" class="form-control">
                            <option> Please Select... </option>
                            @foreach($contests as $contest)
                                @if (Request::get('end_at') == $contest->id)
                                    <option value="{{ $contest->id }}" selected> {{ $contest->name }} </option>
                                @else
                                    <option value="{{ $contest->id }}" > {{ $contest->name }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>



            <div class="card-body">
                <canvas id="ratingChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('script-after')
    <script>
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        var ctx = document.getElementById('ratingChart');

        var config = {
            type: 'line',
            data: {
                labels: JSON.parse('@json($ratings['labels'])'),
                datasets: [{
                    label: '{{ $student->name }}',
                    borderColor: window.chartColors.blue,
                    backgroundColor: window.chartColors.blue,
                    data: JSON.parse('@json($ratings['data'])'),
                    fill: false,
                },]
            },
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Contest Info'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                }
            }
        };


        window.myLine = new Chart(ctx, config);

    </script>
@endsection
