<?php
/**
 *
 * Created by Dream.
 * User: Boxjan
 * Datetime: 2019-02-03 19:40
 */
?>

@extends('admin.layout')

@section('main')

    <div class="container">
        <div class="el-card">
            <div class="el-card__header">
                <div class="title h2">{{ __('Student List') }}</div>
            </div>

            <div class="el-card__body">

                <el-table :data="userList" style="width: 100%" stripe>
                    <el-table-column type="index" width="150"></el-table-column>
                    <el-table-column prop="username" label="username" width="180"> </el-table-column>
                    <el-table-column prop="nickname" label="nickname" width="180"> </el-table-column>
                    <el-table-column prop="created_at" label="create time" width="150"> </el-table-column>


                    <el-table-column  label="options" width="330">
                        <template slot-scope="scope">
                            <el-button size="mini"
                                       @click="handleEditNickname(scope.$index, scope.row)">{{ __('Edit nickname') }}</el-button>
                            <el-button size="mini"
                                       @click="handleEditPassword(scope.$index, scope.row)">{{ __('Edit password') }}</el-button>
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

            </div>
        </div>
    </div>

    <div id="dialog-group">
        <el-dialog title="Add a student" :visible.sync="addDialog" width="40%" :show-close="false">

            <form id="addUserForm" onsubmit="vue.handleAddSave(); return false}">
                <div class="form-group mb-2">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="username">
                </div>

                <div class="form-group mb-2">
                    <label for="nickname" class="col-sm-2 col-form-label">Nickname</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="nickname" >
                </div>

                <div class="form-group mb-2">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"  >
                </div>

                <div class="form-group mb-2">
                    <label for="password-confirm" class="col-sm-2 col-form-label">Password Confirm</label>
                    <input type="password" class="form-control" id="password-confirm" name="password_confirmation">
                </div>

                <div slot="footer" class="dialog-footer">
                    <input type="reset" id="addFormReset" style="display: none">
                    <button type="button" @click="handleAddCancel()" class="el-button" >取 消</button>
                    <button type="button" @click="handleAddSave();" class="el-button el-button--primary"> 确 定 </button>
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
                    addDialog: false,
                    userList: JSON.parse(@json($users->toJson())),
                }
            },

            methods: {
                handleAddSave() {
                    const addData = $('#addUserForm').serializeArray();
                    this.addDialog = false;

                    let params = {};
                    for (x in addData) {
                        params[addData[x].name] = addData[x].value;
                    }

                    axios.post('{{ route('admin.user.index') }}', params)
                        .then((response) => {
                            this.$message({message: 'Add success', type: 'success'});
                            this.userList.push({
                                id: response.data.id,
                                username: params.username,
                                nickname: params.nickname,
                                created_at: response.data.date,
                            });
                            $('#addFormReset').click();
                        }).catch((error) => {
                            this.$message({message: 'Add fail', type: 'error'});
                            console.log(error)
                        });
                },

                handleAddCancel() {
                    $('#addFormReset').click();
                    this.addDialog = false;
                },


                handleDelete(index, id) {
                    this.$confirm('It will delete the User, are you sure?', 'Notice', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        type: 'warning'
                    }).then(()=> {
                        axios.post( '{{ route('admin.user.index') }}' + '/' + id, {
                            _method: 'Delete',
                        }).then( (response) => {
                            this.$message({message: 'Delete success',type: 'success'});
                            this.userList.splice(index, 1);
                        }).catch((error) => {
                            this.$message({message: 'Delete fail', type: 'error'});
                            console.log(error)
                        });
                    }).catch(() => {
                        this.$message({type: 'info', message: 'Cancel'});
                    })

                },

                handleEditNickname(index, row) {
                    this.$prompt('Please input a the new nickname', 'Input', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        inputPattern: /.+/,
                        inputErrorMessage: 'Nothing input',
                        inputValue: row.nickname
                    }).then(({value}) => {
                        axios.post('{{ route('admin.user.index') }}' + '/' + row.id,
                            {_method: "PUT", nickname: value})
                            .then((response) => {
                                this.$message({message: 'Update success', type: 'success'});
                                this.userList[index].name = value
                            }).catch((error) => {
                            this.$message({message: 'Update fail', type: 'error'});
                            console.log(error)
                        });
                    }).catch((error) => {
                        this.$message({type: 'info', message: 'Cancel'});
                        console.log(error)
                    });
                },

                handleEditPassword(index, row) {
                    this.$prompt('Please input a the new Password', 'Input', {
                        inputPattern: /.{6,}/,
                    }).then(({value}) => {
                        axios.post('{{ route('admin.user.index') }}' + '/' + row.id,
                            {_method: "PUT", password: value})
                            .then((response) => {
                                this.$message({message: 'Update success', type: 'success'});
                            }).catch((error) => {
                            this.$message({message: 'Update fail', type: 'error'});
                            console.log(error)
                        });
                    }).catch((error) => {
                        this.$message({type: 'info', message: 'Cancel'});
                        console.log(error)
                    });
                }
            }
        }
    </script>
@endsection

