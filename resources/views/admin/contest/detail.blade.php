@extends('admin.layout')

@section('main')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <a class="h3"> {{ __("Contest Detail") }} </a>
                    </div>

                    <div class="card-body">


                        <form method="post"  onsubmit="return vue.handleSubmitCheck()">
                            @method('PUT')
                            @csrf

                            <div class="form-group row">
                                <label for="contest-name" class="col-sm-3 col-form-label"> {{__("Contest Name")}} </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control{{ $errors->has('contest-name') ? ' is-invalid' : '' }}"
                                           id="contest-name" placeholder="{{__("Contest Name")}}" name="contest-name" maxlength="254"
                                           value="{{ $contest->name }}">
                                    @if ($errors->has('contest-name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('contest-name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="contest-url" class="col-sm-3 col-form-label"> {{__("Contest Url")}} </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                                           id="contest-url" placeholder="{{__("Contest Url")}}" name="url" maxlength="200"
                                           value="{{ $contest->url }}">
                                    @if ($errors->has('url'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="contest-time" class="col-sm-3 col-form-label"> {{__("Contest Time")}} </label>
                                <div class="col-md-6">
                                    <input type="hidden" class="form-control" id="contest-time" name="contest-time" v-model="contestTime">
                                    <el-date-picker
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
                                    <input type="hidden" class="form-control" id="contest-rank" name="contest-rank" v-model="JSON.stringify(studentsRank)">

                                    <template>
                                        <el-table :data="studentsRank" style="width: 100%" border size="mini">
                                            <el-table-column width="145" prop="student_id">
                                                <template slot="header" slot-scope="scope">
                                                    <el-button @click="handleAddStudentRank()" size="small">
                                                        {{ __("Add") }}
                                                    </el-button>
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="name" label="{{ __("Name") }}" width="145">
                                                <template slot-scope="scope" size="mini">
                                                    <el-input v-if="scope.row.id == ''" v-model="scope.row.name" placeholder="Name" ></el-input>
                                                    <el-input v-else v-model="scope.row.name" placeholder="Name" :disabled="true"></el-input>
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="rank" label="{{ __("Rank") }}" width="75">
                                                <template slot-scope="scope" size="mini">
                                                    <input class="el-input--mini el-input__inner el-input" v-model="scope.row.rank" placeholder="">
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="rank" label="{{ __("Solved") }}" width="75">
                                                <template slot-scope="scope" size="mini">
                                                    <input class="el-input--mini el-input__inner el-input" v-model="scope.row.solved" placeholder="">
                                                </template>
                                            </el-table-column>
                                            <el-table-column label="{{ __("Opt") }}" width="75">
                                                <template slot-scope="scope">
                                                    <el-button @click.native.prevent="deleteRow(scope.$index)" type="text" size="small">
                                                        {{ __('Remove') }}
                                                    </el-button>
                                                </template>
                                            </el-table-column>

                                        </el-table>
                                    </template>

                                    <label for="import" class="col-sm-3 col-form-label"> {{__("Import")}} </label>
                                    <el-select v-model="importSelected" filterable placeholder="请选择" @change="value => importSelect(value)">
                                        <el-option
                                                v-for="item in groupOptions"
                                                :key="item.id"
                                                :label="item.name"
                                                :value="item.id">
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>

                            <input type="submit" value="Submit" class="btn btn-primary">
                        </form>
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
                    groupOptions: JSON.parse('@json($groups)'),
                    importSelected:'',

                }
            },
            methods: {
                importSelect(group) {
                    $.ajaxSettings.async = false;
                    let response = $.post('{{ route('admin.get.student.list.by.group') }}', {group: group});
                    console.log(response);
                    if (response.status !== 200) {
                        this.$message({
                            type: 'warn',
                            message: response.responseJSON.message,
                        });
                        return false
                    }

                    for (let i in response.responseJSON) {
                        let res = response.responseJSON[i];
                        let flag = 0;
                        for (let j in vue.studentsRank) {
                            let item = vue.studentsRank[j];
                            if (item.id === res.id) {flag = 1; break}
                        }

                        if (flag === 1) continue;

                        vue.studentsRank.push({
                            id: res.id,
                            student_id: res.student_id,
                            name: res.name,
                            rank: '',
                            solved: '',
                        });

                    }

                    this.importSelected = '';

                },

                handleAddStudentRank() {
                    this.studentsRank.push({
                        id: '',
                        student_id: '',
                        rank:'',
                        student: '',
                        solved: '',
                    });
                },

                deleteRow(index) {
                    this.studentsRank.splice(index, 1)
                },

                handleSubmitCheck() {
                    let noHaveRankStudents = [];
                    let noIdStudents = [];

                    for (let i = 0; i < this.studentsRank.length; i++) {
                        if (this.studentsRank[i].id == '' && this.studentsRank[i].name != '')
                            noIdStudents.push(this.studentsRank[i].name);
                        if (this.studentsRank[i].rank == '' && this.studentsRank[i].name != '')
                            noHaveRankStudents.push(this.studentsRank[i].name);
                    }

                    if (noHaveRankStudents.length === 0 && noIdStudents.length === 0) return true;


                    let result = confirm(
                        'These student: ' + JSON.stringify(noIdStudents) +' Not in Database, it will be create; ' +
                        'These student: ' + JSON.stringify(noHaveRankStudents) + ' don\'t have ranking;' +
                        ' are you sure?');
                    if (! result) {
                        this.$message({
                            type: 'info',
                            message: 'Cancel..'
                        });
                        return false;
                    }

                    return true;

                }
            }
        }
    </script>
@endsection
