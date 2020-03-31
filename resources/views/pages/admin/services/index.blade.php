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
                    <div class="py-2">
                        <a href="{{ route('service.create') }}" class="btn btn-primary" > Create Service</a>
                    </div>
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Name</th>
                                <th>Min Qty</th>
                                <th>Max Qty</th>
                                <th>Min Qty Charges</th>
                                <th>Price</th>
                                <th>Charges</th>
                                <th>Sample Qty</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->s_name}} </td>
                                    <td> {{ $item->min_qty}} </td>
                                    <td> {{ $item->max_qty}} </td>
                                    <td> {{ $item->min_qty_charges }} </td>
                                    <td> {{ $item->s_price }} </td>
                                    <td> {{ $item->s_charges }} </td>
                                    <td> {{ $item->s_sample_qty }} </td>
                                    <td> <a href='{{ asset("services/$item->img_url") }}'>Icon</a></td>
                                    <td> {{ $item->s_status }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('service.show',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('service.edit',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-edit"></i></a>
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