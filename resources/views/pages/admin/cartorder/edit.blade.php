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
                        Order Proceed Form
                    </h3>
                </div>
                @if($form->fields->count() > 0)
                {!! form_start($form) !!}
                <div class="card-body">
                    {!! form_rest($form) !!}
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Proceed</button>
                    <button type="submit" class="btn btn-default float-right">Cancel</button>
                </div>
                {!! form_end($form) !!}
                @else
                    <div class="row">
                        <h1> ORDER PROCEED COMPLETED </h1>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection