@extends('adminlte::page')

@section('content')

<section class="content">
    <div class="row">
        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Role Form
                    </h3>
                </div>
                {!! form_start($form) !!}
                <div class="card-body">
                    {!! form_rest($form) !!}
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Save</button>
                    <button type="submit" class="btn btn-default float-right">Cancel</button>
                </div>
                {!! form_end($form) !!}
            </div>
        </div>
    </div>
</section>
@endsection