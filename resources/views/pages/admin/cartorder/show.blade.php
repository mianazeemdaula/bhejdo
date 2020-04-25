@extends('adminlte::page')

@section('content')

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <!-- title row -->
                <div class="row">
                  <div class="col-12">
                    <h4>
                      <i class="fas fa-globe"></i> ORDER INVOICE.
                      <small class="float-right">Date: {{ $order->created_at }}</small>
                    </h4>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- info row -->
                <div class="row invoice-info">
                  <div class="col-sm-4 invoice-col">
                    CONSUMER
                    <address>
                      <strong>{{ $order->consumer->name }}</strong><br>
                      Phone: {{ $order->consumer->mobile }}<br>
                      Email: {{ $order->consumer->email }}
                    </address>
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 invoice-col">
                    STORE
                    <address>
                        @isset($order->store)
                          <strong>{{ $order->store->name }}</strong><br>
                          Phone: {{ $order->store->mobile }}<br>
                          Email: {{ $order->store->email }}
                        @else
                          NOT ASSIGNED
                        @endisset
                    </address>
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 invoice-col">
                    LIFTER
                    <address>
                        @isset($order->lifter)
                          <strong>{{ $order->lifter->name }}</strong><br>
                          Phone: {{ $order->lifter->mobile }}<br>
                          Email: {{ $order->lifter->email }}
                        @else
                          NOT ASSIGNED
                        @endisset
                    </address>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
  
                <!-- Table row -->
                <div class="row">
                  <div class="col-12 table-responsive">
                    <table class="table table-striped">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach ($order->details as $item)
                        <tr>
                          <td>{{ $item->product->name }}</td>
                          <td>{{ $item->qty }}</td>
                          <td>{{ $item->price }}</td>
                          <td>{{ $item->price * $order->qty }}</td>
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
  
                <div class="row">
                  <!-- accepted payments column -->
                  <div class="col-6">
                    <p class="lead">Payment Details:</p>
                  </div>
                  <!-- /.col -->
                  <div class="col-6">
  
                    <div class="table-responsive">
                      <table class="table">
                        <tbody><tr>
                          <th style="width:50%">Subtotal:</th>
                          <td></td>
                        </tr>
                        <tr>
                          <th>Delivery Charges</th>
                          <td>{{ $order->charges }}</td>
                        </tr>
                        <tr>
                            <th>Store Amount</th>
                            <td>-{{ $order->store_amount }}</td>
                          </tr>
                        <tr>
                          <th>Total Pay by customer:</th>
                          <td>{{ $order->payable_amount }}</td>
                        </tr>
                      </tbody></table>
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
        </div>
    </div>
</section>
@endsection
@section('plugins.Datatables', true)
@section('js')
<script>
$(function () {
});
</script>
@endsection