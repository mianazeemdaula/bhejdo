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
                        Subscription(s)
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Consumer</th>
                                <th>Lifter</th>
                                <th>Qty</th>
                                <th>Shift</th>
                                <th>Delivery Time</th>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Created</th>
                                <th>Dated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->order->consumer->mobile}} <br/> {{ $item->order->consumer->name}} </td>
                                    <td> {{ $item->order->lifter->mobile}} <br/> {{ $item->order->lifter->name}} </td>
                                    <td> {{ $item->qty }} </td>
                                    <td> {{ $item->shift }} </td>
                                    <td> {{ $item->delivery_time }} </td>
                                    <td> {{ $item->subscribe_type }} </td>
                                    <td> {{ $item->days }} </td>
                                    <td> {{ $item->status }} </td>
                                    <td><a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}">Map</a></td>
                                    <td> {{ $item->created_at }} </td>
                                    <td> {{ $item->updated_at }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('order.show',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('order.edit',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-edit"></i></a>
                                        <a href="{{ url('order/livepartners/'.$item->id) }}" class="btn-sm btn-default"><i class="fas fa-trash"></i></a>
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