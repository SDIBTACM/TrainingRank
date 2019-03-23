@extends('admin.dialog')

@section('main')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <a class="h3"> {{ "Contest  Detail" }} </a>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                                <label for="contest-name" class="col-sm-3 col-form-label"> {{__("Contest Name")}} </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="contest-name" placeholder="{{__("Contest Name")}}" name="contest-name" readonly value="{{ $contest->name }}">

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="contest-time" class="col-sm-3 col-form-label"> {{__("Contest Time")}} </label>
                                <div class="col-md-6">
                                    <el-date-picker readonly
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                            v-model="contestTime"
                                            type="datetime"
                                            placeholder="{{ __("Contest Time") }}">
                                    </el-date-picker>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="contest-time" class="col-sm-3 col-form-label"> {{__("Contest Rank")}} </label>
                                <div class="col-md-8">
                                    <template>
                                        <el-table :data="studentsRank" style="width: 100%">
                                            <el-table-column prop="name" label="{{ __("Student Name") }}" width="180">
                                            </el-table-column>
                                            <el-table-column prop="student_id" label="{{ __("Student Id") }}" width="180">
                                            </el-table-column>
                                            <el-table-column prop="rank" label="{{ __("Student Rank") }}" width="75">
                                            </el-table-column>
                                        </el-table>
                                    </template>

                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let data = {
            data: function () {
                return {
                    contestTime: '{{ $contest->start_time }}',
                    studentsRank: JSON.parse('@json($contest->rank)'),
                }
            },
            methods: {

            }
        }
    </script>
@endsection