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
                <h2 class="card-title"> Contest List </h2>
            </div>

            <div class="card-body">
                <el-table :data="contests" style="width: 100%" stripe>
                    <el-table-column type="index" width="90"></el-table-column>
                    <el-table-column prop="name" label="{{ __('Contest Name') }}" width="210"> </el-table-column>
                    <el-table-column prop="start_time" label="{{ __('Contest start time') }}" width="210"> </el-table-column>
                    <el-table-column prop="created_at" label="create time" width="210"> </el-table-column>

                    <el-table-column label="options" width="180">
                        <template slot-scope="scope">
                            <a :href="'{{ route('contest.index') }}' + '/' + scope.row.id">
                                <el-button size="mini" type="info" >
                                    {{ __( 'Detail' ) }}
                                </el-button></a>
                        </template>
                    </el-table-column>

                </el-table>

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
                    contests: JSON.parse( '@json($contests)' ).data ,
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

