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
                        Service(s)
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Name</th>
                                <th>Account</th>
                                <th>Last Updated</th>
                                <th>Location</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lifters as $item)
                                <tr>
                                    <td> {{ $item->lifter_id}} </td>
                                    <td> {{ $item->name}} </td>
                                    <td> {{ $item->account_type}} </td>
                                    <td> {{ $item->last_update}} </td>
                                    <td>  </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('plugins.Datatables', true)
@section('js')
<script>
$(function () {
    $("#example1").DataTable();
});
</script>
@endsection