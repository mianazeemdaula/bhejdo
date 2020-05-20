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
                        Cities
                    </h3>
                </div>
                <div class="card-body">
                    @role('super-admin|admin')
                    <div class="py-2">
                        <a href="{{ route('admin.city.create') }}" class="btn btn-primary" > Create City</a>
                    </div>
                    @endrole
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Name</th>
                                <th>Open Time</th>
                                <th>Close Time</th>
                                <th>Bullet</th>
                                <th>Normal</th>
                                <th>Bullet Time</th>
                                <th>Normal Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $item)
                                <tr>
                                    <td> {{ $item->id }} </td>
                                    <td> {{ $item->name }} </td>
                                    <td> {{ $item->open_time }} </td>
                                    <td> {{ $item->close_time }} </td>
                                    <td> {{ $item->bullet_charges }} </td>
                                    <td> {{ $item->delivery_charges }} </td>
                                    <td> {{ $item->bullet_delivery_time }} </td>
                                    <td> {{ $item->normal_delivery_time }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('admin.city.show',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.city.edit',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-default"><i class="fas fa-trash"></i></button>
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