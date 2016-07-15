@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body">
                    @if (session('auth_status'))
                        <div class="alert alert-{{ session('auth_status.alert') }}">
                            {{ session('auth_status.message') }}
                        </div>
                    @endif

                    Your Application's Landing Page.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
