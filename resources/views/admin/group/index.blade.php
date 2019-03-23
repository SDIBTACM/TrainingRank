<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-21 20:21
 */
?>

@extends('admin.layout')

@section('main')
    <div class="container">
        <div class="el-card">
            <div class="el-card__header">
                <div class="title h2">{{ __($title) }}</div>
            </div>

            <div class="el-card__body">

                <el-table :data="groups" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Name') }}" width="150"> </el-table-column>
                    <el-table-column prop="created_at" label="create time" width="150"> </el-table-column>

                    <el-table-column  label="options" width="300">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleDelete(scope.$index, scope.row.id)">{{ __('Delete') }}</el-button>
                        </template>
                    </el-table-column>

                </el-table>

                <div style="margin-top: 20px">
                    <el-button @click="handleAdd()">Add One</el-button>
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
                    groups: JSON.parse('@json($groups)'),
                }
            },
            methods: {
                handleDelete(index, id) {
                    this.$confirm('It will delete the Student, are you sure?', 'Notice', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        type: 'warning'
                    }).then(() => {
                        axios.post('{{ route('admin.group.index') }}' + '/' + id, {_method: 'Delete'})
                            .then((response) => {
                                this.$message({message: 'Delete success', type: 'success',});
                                this.groups.splice(index, 1);
                            })
                            .catch((error) => {
                                this.$message({message: 'Delete fail', type: 'error'});
                                console.log(error)
                            });
                    }).catch(() => {
                        this.$message({type: 'info', message: 'Cancel'});
                    })
                },

                handleAdd() {
                    this.$prompt('Please input a the new Group Name', 'Input', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        inputPattern: /.+/,
                        inputErrorMessage: 'Nothing input',
                    }).then(({value}) => {
                        axios.post('{{ route('admin.group.store') }}',
                            {name: value}
                        ).then((response) => {
                            this.$message({message: 'Update success', type: 'success'});
                            this.groups.push({id: response.data.id, name: value, created_at: response.data.date});
                        }).catch((error) => {
                            this.$message({message: 'Update' + '', type: 'error'});
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