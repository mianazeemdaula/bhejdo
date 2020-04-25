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
                        Order(s)
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Consumer</th>
                                <th>Store</th>
                                <th>Lifter</th>
                                <th>Bullet?</th>
                                <th>Price</th>
                                <th>Charges</th>
                                <th>Type</th>
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
                                    <td> {{ $item->consumer->mobile}} <br/> {{ $item->consumer->name}} </td>
                                    <td>
                                        @isset($item->store)
                                        {{ $item->store->mobile}} <br/> {{ $item->store->name}}
                                        @else
                                            Not Assigned
                                        @endisset
                                    </td>
                                    <td>
                                        @isset($item->lifter)
                                        {{ $item->lifter->mobile}} <br/> {{ $item->lifter->name}}
                                        @else
                                            Not Assigned
                                        @endisset
                                    </td>
                                    <td> {{ $item->bullet_delivery }} </td>
                                    <td> {{ $item->charges }} </td>
                                    <td> {{ $item->delivery_time }} </td>
                                    <td> {{ $item->address->address }} </td>
                                    <td> {{ $item->payable_amount }} </td>
                                    <td> {{ $item->status }} </td>
                                    {{-- <td><a href="https://www.google.com/maps/search/?api=1&query={{ $item->latitude }},{{ $item->longitude }}">Map</a></td> --}}
                                    <td> {{ $item->created_at }} </td>
                                    <td> {{ $item->updated_at }} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="{{ route('admin.order.show',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-eye"></i></a>
                                        {{-- <a href="{{ route('order.edit',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-edit"></i></a>
                                        <a href="{{ url('order/livepartners/'.$item->id) }}" class="btn-sm btn-default"><i class="fas fa-trash"></i></a> --}}
                                        
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