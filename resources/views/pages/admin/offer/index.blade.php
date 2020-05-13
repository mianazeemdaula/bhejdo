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
                        <a href="{{ route('offer.create') }}" class="btn btn-primary" > Create Offer</a>
                    </div>
                    <table class="table table-sm dataTable" id="example1">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Title</th>
                                <th>Promo Code</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection as $item)
                                <tr>
                                    <td> {{ $item->id}} </td>
                                    <td> {{ $item->title}} </td>
                                    <td> {{ $item->promo_code}} </td>
                                    <td> {{ $item->amount}} </td>
                                    <td> {{ $item->type}} </td>
                                    <td> {{ $item->category}} </td>
                                    <td> {{ $item->expiry_date}} </td>
                                    <td> {{ $item->status}} </td>
                                    <td> {{ $item->created_at}} </td>
                                    <td> {{ $item->updated_at}} </td>
                                    <td> 
                                      <div class="btn-group">
                                        <a href="#" class="btn-sm btn-default"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('offer.edit',[$item->id]) }}" type="button" class="btn-sm btn-default"><i class="fas fa-edit"></i></a>
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