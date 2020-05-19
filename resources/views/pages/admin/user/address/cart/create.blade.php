@extends('adminlte::page')

@section('content')

<section class="content">
    <div class="row">
        <div class="col-12">
            @if (session('status'))
            <div class="alert alert-success">
                <div class="row">
                    <div class="col-md-10">{{ session('status')[0] }}</div>
                    <div class="col-md-2 float-right"><a target="_blank" href="{{ route('labvoucher.show',session('status')[1]) }}">Print Slip</a></div>
                </div>
            </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Cart Order
                    </h3>
                </div>
                {!! form_start($form) !!}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! form_row($form->voucher) !!}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <label for="medicine" class="control-label required">Products</label>
                            <select name="labs" id="labs" class="form-control">
                                @foreach ($products as $item)
                                    <option data-price="{{ $item->sale_price }}" value="{{ $item->id }}"> {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="medicine" class="control-label required">Qty</label>
                            <input type="text" name="qty" id="qty" class="">
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <a style="margin-top:30px" type="submit" class="btn btn-info btn-block" id="add-row">Add</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <table id="labs-table" class="table">
                            <thead>
                                <tr>
                                    <td style="width: 75%">Name</td>
                                    <td>Qty</td>
                                    <td>Price</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <!-- accepted payments column -->
                    <div class="col-6">
                    </div>
                    <!-- /.col -->
                    <div class="col-6">
    
                      <div class="table-responsive">
                        <table class="table">
                          <tbody>
                          <tr>
                            <th>Total:</th>
                            <td><h3 id='total'></h3></td>
                          </tr>
                        </tbody></table>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Save</button>
                    <button type="submit" class="btn btn-default float-right">Cancel</button>
                </div>
                {!! form_end($form) !!}
            </div>
        </div>
    </div>
</section>
@endsection

@section('plugins.Select2', true)
@section('js')
<script>
    $(document).ready(function(){
        $('#labs').select2();
        $('#doctor_id').select2();
        $("#add-row").click(function(){
            var id = $("#labs").val();
            var qty = $("#qty").val();
            var title = $("#labs option:selected").html();
            var price = $("#labs option:selected").data('price');
            var markup = "<tr><td><input value='"+id+"' type='hidden' name='products[]'><input value='"+price+"' type='hidden' name='prices[]'>" + title + "</td><td>" + price + "</td><td><a class='cancel' href='#'>X</a></td></tr>";
            $("#labs-table tbody").append(markup);
            calculateTotal();
        });
        
        function calculateTotal(){
            var sum = 0;
            var qty = 0;
            $("#labs-table tbody").find("tr").each(function(){
                qty += parseInt($(this).find("td:nth-child(1)").text());
                sum += parseInt($(this).find("td:nth-child(2)").text());
            });
            $("#total").html(sum);
        }
        // Find and remove selected table rows
        $('body').on('click', '.cancel', function(){
            $(this).parents("tr").remove();
            calculateTotal();
        });
    });    
</script>
    
@endsection