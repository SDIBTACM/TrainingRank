@extends('admin.layout')

@section('main')

    <div class="container">
        <div class="el-card">
            <div class="el-card__header">
                <div class="title h2">{{ __('Contest List') }}</div>
            </div>

            <div class="el-card__body">

                <el-table :data="contests" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Contest Name') }}" width="180">
                        <template slot-scope="scope">
                            <a :href="scope.row.url">
                                <p >@{{ scope.row.name }}</p>
                            </a>
                        </template>
                    </el-table-column>
                    <el-table-column prop="start_time" label="{{ __('Contest start time') }}" width="150"> </el-table-column>
                    <el-table-column prop="created_at" label="create time" width="150"> </el-table-column>

                    <el-table-column label="options" width="180">
                        <template slot-scope="scope">
                            <a :href="'{{ route('admin.contest.index') }}' + '/' + scope.row.id">
                                <el-button size="mini" type="info">
                                    {{ __( 'Detail' ) }}
                                </el-button>
                            </a>

                            <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row.id)">
                                {{ __('Delete') }}
                            </el-button>
                        </template>
                    </el-table-column>

                </el-table>

                <div style="margin-top: 20px">
                    <a href="{{route('admin.contest.create')}}"><el-button>{{ __('Add') }}</el-button></a>
                </div>

                <div class="link justify-content-center">
                    {{ $contests->appends(Request::query())->links() }}
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
                    contests: JSON.parse('@json($contests)').data,
                }
            },

            methods: {
                handleDetail(id) {
                    window.open('{{ route('admin.contest.index') }}' + '/' + id, '');
                },

                handleDelete(index, id) {

                    this.$confirm('It will delete the Contest, are you sure?', 'Notice', {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        type: 'warning'
                    }).then( ()=> {
                        axios.post( '{{ route('admin.contest.index') }}' + '/' + id, {
                            _method: 'Delete',
                        }).then( (response) => {
                            this.$message({
                                message: 'Delete success',
                                type: 'success',
                            });
                            this.contests.splice(index, 1);
                        }).catch((error) => {
                            this.$message({
                                message: 'Delete fail' + error,
                                type: 'error',
                            });
                            console.log(error)
                        });
                    }).catch(() => {
                        this.$message({
                            type: 'info',
                            message: 'Cancel'
                        });
                    })

                }
            }
        }
    </script>
@endsection