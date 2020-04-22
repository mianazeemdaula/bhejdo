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
                        Medicine(s)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="py-2">
                        <a href="{{ route('user.create') }}" class="btn btn-primary" > Create User</a>
                    </div>
                    <table class="table table-sm dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Rols</th>
                                <th>Created</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->name}} </td>
                                    <td> {{ $item->email}} </td>
                                    <td> @isset($item->getRoleNames()[0])
                                            {{ $item->getRoleNames()[0] }}
                                        @endisset</td>
                                    <td> {{ $item->created_at}} </td>
                                    <td> {{ $item->updated_at}} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="#" class="btn-sm btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('user.edit',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn-sm btn-default"><i class="fas fa-trash"></i></a>
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