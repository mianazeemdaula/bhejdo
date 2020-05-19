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
                        Users(s)
                    </h3>
                </div>
                <div class="card-body">
                    @role('super-admin|admin')
                    <div class="row">
                        <div class="col-md-10">
                            <a href="{{ route('user.create') }}" class="btn btn-primary" > Create User</a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ url("user/$type/download/csv") }}" class="btn btn-default float-right" >CSV Download</a>
                        </div>
                    </div>
                    @endrole
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Rols</th>
                                <th>Status</th>
                                <th>City</th>
                                <th>Created</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->name}} </td>
                                    <td> {{ $item->mobile}} </td>
                                    <td> {{ $item->email}} </td>
                                    <td> {{ $item->getRoleNames()[0] }} </td>
                                    <td> {{ $item->status }} </td>
                                    <td> {{ $item->city->name }} </td>
                                    <td> {{ $item->created_at}} </td>
                                    <td> {{ $item->updated_at}} </td>
                                    <td> 
                                      <div class="btn-group">
                                        @if($item->hasRole('consumer'))<a href="{{ route('user.cart.create',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-cart"></i></a>@endif
                                        <a href="{{ route('user.show',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('user.edit',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-edit"></i></a>
                                        <a href="{{ url('user/notification/'.$item->id) }}" class="btn-sm btn-default"><i class="fas fa-trash"></i></a>
                                      </div>
                                    </td>
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