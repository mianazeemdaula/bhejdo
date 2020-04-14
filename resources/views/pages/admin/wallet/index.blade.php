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
                    @role('super-admin|admin')
                    <div class="py-2">
                        <a href="{{ route('user.wallet.create',[$user->id]) }}" class="btn btn-primary" >Create Entry</a>
                    </div>
                    @endrole
                    <table class="table table-bordered table-striped dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>created</th>
                                <th>updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->description}} </td>
                                    <td> {{ $item->type}} </td>
                                    <td> {{ $item->amount}} </td>
                                    <td> {{ $item->balance }} </td>
                                    <td> {{ $item->created_at }} </td>
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