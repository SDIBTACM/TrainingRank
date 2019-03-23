<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-20 20:36
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
                <h4 class="card-subtitle"> {{ __('Contest Name') }}: {{ $contest->name }}</h4>
                <p class="card-subtitle"> {{ __('Competing time') }}: {{ $contest->start_time }} </p>
            </div>

            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Rank</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Student Id</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ranks as $rank)
                        <tr>
                            <th scope="row">{{ $rank['rank'] }}</th>
                            <td>{{ $rank['student']['name'] }}</td>
                            <td>{{ $rank['student']['student_id'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const data = {
            data: function () {
                return {

                }
            },
            methods: {},
            mounted() {
            }
        }
    </script>
@endsection

@section('script-after')
    <script>

    </script>
@endsection
