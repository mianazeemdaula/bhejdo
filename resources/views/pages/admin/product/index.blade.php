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
                        Products(s)
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Name</th>
                                <th>Urdu Name</th>
                                <th>Contract Price</th>
                                <th>Markeet Price</th>
                                <th>City</th>
                                <th>Weight</th>
                                <th>Unit</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->name}} </td>
                                    <td> {{ $item->urdu_name}} </td>
                                    <td> {{ $item->contract_price}} </td>
                                    <td> {{ $item->markeet_price }} </td>
                                    <td> {{ $item->city->name }} </td>
                                    <td> {{ $item->weight }} </td>
                                    <td> {{ $item->unit }} </td>
                                    <td> <a href='{{ asset("product/$item->img_url") }}'>Icon</a></td>
                                    <td> {{ $item->status }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('admin.product.show',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.product.edit',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-edit"></i></a>
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