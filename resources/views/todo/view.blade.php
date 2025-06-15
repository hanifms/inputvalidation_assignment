@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">View Todo</div>

                <div class="card-body">
                    <div class="form-group row mb-3">
                        <label class="col-md-4 col-form-label text-md-right">Title</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $todo->title }}</p>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-4 col-form-label text-md-right">Description</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $todo->description }}</p>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-4 col-form-label text-md-right">Status</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ ucfirst($todo->status) }}</p>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('todo.edit', $todo->id) }}" class="btn btn-primary">
                                Edit
                            </a>
                            <a href="{{ route('todo.index') }}" class="btn btn-secondary">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
