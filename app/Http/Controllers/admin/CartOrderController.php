<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\User;
use App\CartOrder;
use Auth;
use DB;
// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Order\UpdateOrderForm;

class CartOrderController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $collection = CartOrder::all();
        return view('pages.admin.cartorder.index', compact('collection'));
    }

    public function show($id)
    {
        $order = CartOrder::find($id);
        return view('pages.admin.cartorder.show', compact('order'));
    }

    public function edit($id)
    {
        $order = CartOrder::findOrFail($id);
        $form = $this->form(UpdateOrderForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('admin.order.update', $id),
            'model' => $order
        ]);
        if($order->status == 'created'){
            $form->add('store_id', 'select', [
                'choices' => User::role('store')->get()->pluck('name','id')->toArray(),
                'label' => 'Store'
            ]);
            $form->add('status', 'choice', [
                'choices' => ['assigned' => 'Assigned', 'declined' => 'Declined'],
                'expanded' => true,
                'multiple' => false
            ]);
        }
        return view('pages.admin.cartorder.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        try {
            /**
             *created -> order created by conusmer
             *assigned -> order assigned to a sotre
             *packed -> order packed by the sotre
             *findrider -> find rider for delivery
             *picked -> order picked by rider
             *drop -> order dorp at door step of consumer
             *complete -> order completed and reviewd by consumer
             *declined -> service not availabe on that area
             *canceled -> order canceled by the customer
             */
            DB::beginTransaction();
            $status = strtolower($request->status);
            if($status == 'assigned'){
                $user = $request->store_id;
            }else if($status == 'picked'){
                $user = $request->lifter_id;
            }
            $response = \App\Helpers\OrderProcess::updateCartOrder($id, $status, $user);
            DB::commit();
            return (string) $response;
            return redirect()->back()->with('status', 'Order updated successfully!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
        }
    }

    public function create()
    {
        $form = $this->form(ProductForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('product.store'),
        ]);
        return view('pages.admin.product.create', compact('form'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $form = $this->form(ProductForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }

            $service = new Product();
            $service->category_id = $request->category_id;
            $service->city_id = Auth::user()->city_id;
            $service->company_id = Auth::id();
            $service->name = $request->name;
            $service->urdu_name = $request->urdu_name;
            $service->contract_price = $request->contract_price;
            $service->markeet_price = $request->markeet_price;
            $service->weight = $request->weight;
            $service->unit = $request->unit;
            if($request->has('image')){
                $cover = $request->file('image');
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('product'), $imageName);
                //\Storage::disk('public')->put($imageName, $cover);
                $service->img_url = $imageName;
            }
            $service->save();
            DB::commit();
            return redirect()->back()->with('status', 'Product Created!');
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('status', "Exception Problem $ex")->withInput();
        }
    }
}
