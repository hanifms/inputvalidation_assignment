@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}<br>
                    <!--button when clicked it will redirect to the todo list page-->
                    <a href="{{ route('todo.index') }}" class="btn btn-primary mb-3">
                        {{ __('Go to Todo List') }}
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
