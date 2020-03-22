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
                        Ledger
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Consumer</th>
                                <th>Lifter</th>
                                <th>Service</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Charges</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Dated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->consumer->mobile}} </td>
                                    <td> {{ $item->lifter->mobile}} </td>
                                    <td> {{ $item->service->s_name}} </td>
                                    <td> {{ $item->qty }} </td>
                                    <td> {{ $item->price }} </td>
                                    <td> {{ $item->charges }} </td>
                                    <td> {{ $item->type }} </td>
                                    <td> {{ $item->status }} </td>
                                    <td><a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}">Map</a></td>
                                    <td> {{ $item->updated_at }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('order.show',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('order.edit',[$item->id]) }}" type="button" class="btn btn-default"><i class="fas fa-edit"></i></a>
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