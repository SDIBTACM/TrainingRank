<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-20 20:07
 */?>

@extends('layout')

@section('main')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"> {{ __($title) }} </h2>
            </div>

            <div class="card-body">
                <form method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="studentName" class="sr-only">Name</label>
                        <input type="text"class="form-control" id="studentName" name="name" placeholder="student name" value="{{ Request::get('name') }}">
                    </div>

                    <div class="form-group mb-2 mx-sm-3">
                        <label for="groupSelect" class="sr-only">oj</label>
                        <select id="groupSelect" class="form-control" name="group">
                            <option value="" {{ Request::get('group', null) == null ? 'selected': '' }}> Origin Group... </option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ Request::get('group', null) == $group->id ? 'selected': '' }}> {{ $group->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2 mx-sm-3">
                        <input type="submit"class="form-control btn btn-primary" id="SubmitButton" value="Search">
                    </div>
                </form>

                <el-table :data="students" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Student Name') }}" width="150"> </el-table-column>
                    <el-table-column prop="student_id" label="{{ __('Student Id') }}" width="150"> </el-table-column>
                    <el-table-column prop="rating" label="{{ __('Rating') }}" width="150"> </el-table-column>

                    <el-table-column label="options" width="180">
                        <template slot-scope="scope">
                            <a :href="'{{ route('student.index') }}' + '/' + scope.row.id">
                                <el-button size="mini" type="info" >
                                    {{ __( 'Detail' ) }}
                                </el-button></a>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="link justify-content-center">
                    {{ $students->appends(Request::query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const data = {
            data: function () {
                return {
                    students: JSON.parse( '@json($students)' ).data ,
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