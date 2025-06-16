@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Form Debug Information</div>

                <div class="card-body">
                    <h3>POST Data</h3>
                    <pre>{{ json_encode($post_data, JSON_PRETTY_PRINT) }}</pre>

                    <h3>Request->all()</h3>
                    <pre>{{ json_encode($request_all, JSON_PRETTY_PRINT) }}</pre>

                    <h3>Request Input</h3>
                    <pre>{{ json_encode($request_input, JSON_PRETTY_PRINT) }}</pre>

                    <a href="{{ route('register') }}" class="btn btn-primary">Back to Registration</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
