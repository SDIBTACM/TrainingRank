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
                <form method="GET" >

                    <div class="form-inline">
                        <div class="form-group mb-2 form-inline">
                            <label for="studentName" class="sr-only">Name</label>
                            <input type="text" class="form-control" id="studentName" name="name" placeholder="student name" value="{{ Request::get('name') }}">
                        </div>
                        <div class="form-group mb-2 mx-sm-3 form-inline">
                            <label for="groupSelect" class="sr-only"></label>
                            <select id="groupSelect" class="form-control" name="type" onchange="$('#SubmitButton').click();">
                                <option value="cf_rating" {{ Request::get('type', 'cf_rating') == 'cf_rating' ? 'selected': '' }}> CF Rating </option>
                                <option value="solved" {{ Request::get('type', 'cf_rating') == 'solved' ? 'selected': '' }}> Solved Count </option>
                            </select>
                        </div>
                        <div class="form-group form-inline mb-2 mx-sm-3">
                            <input type="submit" class="form-control btn btn-primary" id="SubmitButton" value="Search">
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="groupSelect" class="sr-only">Group</label>
                        <input type="text" id="groupInput" name="group" placeholder="student name" :value="JSON.stringify(groupSelect)" style="display: none">

                        <div>
                            <el-checkbox-group v-model="groupSelect">
                                <el-checkbox-button v-for="group in groups" :label="group.id" :key="group.id">@{{group.name}}</el-checkbox-button>
                            </el-checkbox-group>
                        </div>
                    </div>

                </form>

                <el-table :data="students" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Student Name') }}" width="150"> </el-table-column>
                    <el-table-column prop="student_id" label="{{ __('Student Id') }}" width="150"> </el-table-column>
                    <el-table-column prop="rating" label="{{ __('Rating') }}" width="150"> </el-table-column>

                    <el-table-column label="options" width="180">
                        <template slot-scope="scope">
                            <a :href="'{{ route('student.index') }}' + '/' + scope.row.id + '?type={{ Request::get('type', 'cf_rating') }}'">
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
                    groups: JSON.parse( '@json($groups)'),
                    groupSelect: JSON.parse( @json(Request::get('group', '[]'))),
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