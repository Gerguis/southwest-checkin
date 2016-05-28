@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="#createModal" data-toggle="modal" class="pull-right"><i class="fa fa-plus"></i></a>
                        <h4 class="panel-title">Users</h4>
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>SW Account Username</th>
                            <th>Created</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(Auth::user()->accounts as $account)
                            <tr>
                                <td>{{$account->username}}</td>
                                <td>{{$account->created_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="createModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal" method="POST" action="{{url("/account")}}">
                    {!! csrf_field() !!}

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Southwest Account</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label">Southwest Username</label>
                            <div class="col-md-10">
                                <input type="text" name="username" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Southwest Password</label>
                            <div class="col-md-10">
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
