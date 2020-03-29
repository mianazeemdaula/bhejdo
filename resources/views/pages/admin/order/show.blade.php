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
                    LIFTER
                    <address>
                        <strong>{{ $order->lifter->name }}</strong><br>
                        Phone: {{ $order->lifter->mobile }}<br>
                        Email: {{ $order->lifter->email }}
                      </address>
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 invoice-col">
                    <b>Order ID:</b> {{ $order->id }}<br>
                    <b>Payment:</b> {{ $order->payable_amount }}<br>
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
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <td>{{ $order->service->s_name }}</td>
                        <td>{{ $order->qty }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->price * $order->qty }}</td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
  
                <div class="row">
                  <!-- accepted payments column -->
                  <div class="col-6">
                    <p class="lead">Payment Methods:</p>
                    <img src="../../dist/img/credit/visa.png" alt="Visa">
                    <img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
                    <img src="../../dist/img/credit/american-express.png" alt="American Express">
                    <img src="../../dist/img/credit/paypal2.png" alt="Paypal">
  
                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                      Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem
                      plugg
                      dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                    </p>
                  </div>
                  <!-- /.col -->
                  <div class="col-6">
                    <p class="lead">Amount Due 2/22/2014</p>
  
                    <div class="table-responsive">
                      <table class="table">
                        <tbody><tr>
                          <th style="width:50%">Subtotal:</th>
                          <td>{{ $order->qty * $order->price }}</td>
                        </tr>
                        <tr>
                          <th>Delivery Charges</th>
                          <td>{{ $order->charges }}</td>
                        </tr>
                        <tr>
                          <th>Service Charges</th>
                          <td>{{ $order->qty * $order->service->s_charges  }}</td>
                        </tr>
                        <tr>
                            <th>Consumer Bonus</th>
                            <td>-{{ $order->qty * 10 }}</td>
                          </tr>
                        <tr>
                          <th>Total:</th>
                          <td>{{ $order->payable_amount }}</td>
                        </tr>
                      </tbody></table>
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
  
                <!-- this row will not appear when printing -->
                <div class="row no-print">
                  <div class="col-12">
                    <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                    <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                      Payment
                    </button>
                    <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                      <i class="fas fa-download"></i> Generate PDF
                    </button>
                  </div>
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
});
</script>
@endsection