<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-17 13:36
 */?>

@extends('admin.layout')

@section('main')
    <div class="container">
        <div class="el-card">
            <div class="el-card__header">
                <div class="title h2">{{ __($title) }}</div>
            </div>

            <div class="el-card__body">

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

                    <div class="form-group mb-2 ">
                        <label for="isShow" class="sr-only">Class</label>
                        <select id="isUpdate" class="form-control" name="isShow">
                            <option value="-1" {{ Request::get('isShow', -1) == -1 ? 'selected': '' }}> is show... </option>
                            <option value="0" {{ Request::get('isShow', -1) == 0 ? 'selected': '' }}> True </option>
                            <option value="1" {{ Request::get('isShow', -1) == 1 ? 'selected': '' }}> False </option>
                        </select>
                    </div>

                    <div class="form-group mb-2 mx-sm-3">
                        <input type="submit"class="form-control btn btn-primary" id="SubmitButton" value="Search">
                    </div>
                </form>

                <el-table :data="studentList" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Name') }}" width="150"> </el-table-column>
                    <el-table-column prop="student_id" label="{{ __('Student Id') }}" width="150"> </el-table-column>
                    <el-table-column prop="Group" label="{{ __('Class') }}" width="150">
                        <template slot-scope="scope">
                            <div class="form-group">
                                <div class="">
                                    <select :id="'categorySwitch-' + scope.row.id" class="form-control" name="category" @change="handleGroupChange(scope.row.id)">
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}"
                                                    :selected=" scope.row.group == {{ $group->id }} ? 'selected' : '' "> {{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </template>
                    </el-table-column>

                    <el-table-column prop="created_at" label="create time" width="150"> </el-table-column>

                    <el-table-column label="showing" width="75">

                        <template slot-scope="scope">
                            <div class="form-group mb-2 ">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" :id="'isShowSwitch-' + scope.row.id"
                                           :checked="scope.row.is_show === 0 ? 'checked' : '' " @change="handleChangeShow(scope.$index, scope.row.id)">
                                    <label :for="'isShowSwitch-' + scope.row.id" class="custom-control-label"></label>
                                </div>
                            </div>
                        </template>

                    </el-table-column>

                    <el-table-column  label="options" width="300">
                        <template slot-scope="scope">
                            <el-button size="mini"
                                       @click="handleEditName(scope.$index, scope.row)">{{ __('Edit Name') }}</el-button>
                            <el-button size="mini"
                                       @click="handleEditStudentId(scope.$index, scope.row)">{{ __('Edit Stu Id') }}</el-button>
                            <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleDelete(scope.$index, scope.row.id)">{{ __('Delete') }}</el-button>
                        </template>
                    </el-table-column>

                </el-table>

                <div style="margin-top: 20px">
                    <el-button @click="addDialog = true">Add One</el-button>
                </div>

                <div class="link justify-content-center">
                    {{ $students->appends(Request::query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="dialog-group">
        <el-dialog title="Add a student" :visible.sync="addDialog" width="40%" :show-close="false">

            <form id="addStudentForm" onsubmit="vue.handleAddSave(); return false">
                <div class="form-group mb-2">
                    <label for="studentName" class="col-sm-2 col-form-label">Name</label>
                    <input type="text" class="form-control" id="studentName" name="name" placeholder="student name">
                </div>

                <div class="form-group mb-2">
                    <label for="studentId" class="col-sm-2 col-form-label">Name</label>
                    <input type="text" class="form-control" id="studentId" name="student_id" placeholder="student id">
                </div>

                <div class="form-group mb-2">
                    <label for="newProblemOjSelect" class="col-form-label">Group</label>
                    <select id="newProblemOjSelect" class="form-control" name="group">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}"> {{ $group->name }} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group custom-control-inline">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="isShowSwitch" name="isShowSwitch" checked>
                        <label class="custom-control-label" for="isShowSwitch"> Will show </label>
                    </div>
                </div>

                <div slot="footer" class="dialog-footer">
                    <input type="reset" id="addFormReset" style="display: none">
                    <button type="button" @click="handleAddCancel()" class="el-button" > {{ __('Cancel') }} </button>
                    <button type="button" @click="handleAddSave();" class="el-button el-button--primary"> {{ __('Confirm') }} </button>
                </div>
            </form>

        </el-dialog>
    </div>

@endsection

@section('script')
    <script>
        const data = {
            data: function () {
                return {
                    studentList: JSON.parse( '@json($students)' ).data ,
                    addDialog: false,
                }
            },

            methods: {
                handleAddCancel() {
                    $('#addFormReset').click();
                    this.addDialog = false;
                },

                handleAddSave() {
                    const addData = $('#addStudentForm').serializeArray();
                    this.addDialog = false;

                    let params = {};
                    for(x in addData) { params[addData[x].name] = addData[x].value;}

                    axios.post( '{{ route('admin.student.index') }}' , params)
                        .then((response) => {
                            this.$message({ message: 'Add success', type: 'success',});
                            this.studentList.push({
                                id: response.data.id,
                                name: params.name,
                                created_at: response.data.date,
                                student_id: params.student_id,
                                is_show: params.isShowSwitch === 'on' ? 0 : 1,
                            });
                            $('#addFormReset').click();
                        })
                        .catch((error) => { this.$message({message: "Error with: " + error, type: 'error'});
                        console.log(error)
                        });
                },

                handleGroupChange(id) {
                    axios.post( '{{ route('admin.student.index') }}' + '/' + id,{
                            _method: 'PUT',
                            'group': $('#categorySwitch-' + id).val(),
                        }
                    ).then( (response) => {
                        this.$message({
                            message: 'Update success',
                            type: 'success',
                        });
                    }).catch((error) => {
                        this.$message({
                            message: 'Update fail',
                            type: 'error',
                        });
                        console.log(error)
                    });

                },

                handleChangeShow(index, id) {
                    let student = {};
                    student.is_show = (this.studentList[index].is_show + 1) % 2;
                    student = Object.assign(student, {_method: "PUT"});

                    axios.post( '{{ route('admin.student.index') }}' + '/' + id, student)
                        .then( (response) => {
                        this.$message({message: 'Update success',type: 'success',});
                        this.studentList[index].is_show = student.is_show;
                    }).catch((error) => {this.$message({message: 'Update fail', type: 'error', });
                        console.log(error)
                    });
                },

                handleDelete(index, id) {
                    this.$confirm('It will delete the Student, are you sure?', 'Notice', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        type: 'warning'
                    }).then(() => {
                        axios.post('{{ route('admin.student.index') }}' + '/' + id, {_method: 'Delete'})
                            .then((response) => {
                                this.$message({message: 'Delete success', type: 'success',});
                                this.studentList.splice(index, 1);
                            })
                            .catch((error) => {
                                this.$message({message: 'Delete fail', type: 'error'});
                                console.log(error)
                            });
                    }).catch(() => {
                        this.$message({type: 'info', message: 'Cancel'});
                    })
                },

                handleEditName(index, row) {
                    this.$prompt('Please input a the new Student Name', 'Input', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        inputPattern: /.+/,
                        inputErrorMessage: 'Nothing input',
                        inputValue: row.name
                    }).then(({value}) => {
                        axios.post('{{ route('admin.student.index') }}' + '/' + row.id,
                            {_method: "PUT", name: value})
                            .then((response) => {
                                this.$message({message: 'Update success', type: 'success'});
                                this.studentList[index].name = value
                            }).catch((error) => {
                            this.$message({message: 'Update fail', type: 'error'});
                            console.log(error)
                        });
                    }).catch((error) => {
                        this.$message({type: 'info', message: 'Cancel'});
                        console.log(error)
                    });
                },

                handleEditStudentId(index, row) {
                    this.$prompt('Please input a the new Student Id', 'Input', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        inputPattern: /.+/,
                        inputErrorMessage: 'Nothing input',
                        inputValue: row.student_id
                    }).then(({value}) => {
                        axios.post('{{ route('admin.student.index') }}' + '/' + row.id,
                            {_method: "PUT", student_id: value})
                            .then((response) => {
                                this.$message({message: 'Update success', type: 'success'});
                                this.studentList[index].student_id = value
                            }).catch((error) => {
                            this.$message({message: 'Update fail', type: 'error'});
                            console.log(error)
                        });
                    }).catch((error) => {
                        this.$message({type: 'info', message: 'Cancel'});
                        console.log(error)
                    });
                },


            }
        }
    </script>
@endsection