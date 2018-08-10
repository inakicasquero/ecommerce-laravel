@extends('admin::layouts.content')

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.users.roles.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-lg btn-primary">
                    {{ __('Add Role') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            {!! $datagrid->render() !!}
        </div>
    </div>
@stop
