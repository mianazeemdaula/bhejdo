<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\User;
use App\CartOrder;
use Auth;
// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Product\UpdateProductForm;

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
        $product = Product::findOrFail($id);
        $form = $this->form(UpdateProductForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('admin.product.update', $id),
            'model' => $product
        ]);
        return view('pages.admin.product.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $form = $this->form(UpdateProductForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->urdu_name = $request->urdu_name;
            $product->description = $request->description;
            $product->min_qty_charges = $request->min_qty_charges;
            $product->contract_price = $request->contract_price;
            $product->sale_price = $request->sale_price;
            $product->markeet_price = $request->markeet_price;
            $product->store_commission = $request->store_commission;
            $product->lifter_commission = $request->lifter_commission;
            $product->bonus_deduction = $request->bonus_deduction;
            $product->oy_commission = $request->oy_commission;
            $product->city_leader_commission = $request->city_leader_commission;
            $product->oyfee_store = $request->oyfee_store;
            $product->oyfee_lifter = $request->oyfee_lifter;
            $product->status = $request->status;
            $product->weight = $request->weight;
            $product->unit = $request->unit;

            if($request->has('image')){
                $cover = $request->file('image');
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('product'), $imageName);
                //\Storage::disk('public')->put($imageName, $cover);
                $product->img_url = $imageName;
            }
            $product->save();
            DB::commit();
            return redirect()->back()->with('status', 'Product Updated!');
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
