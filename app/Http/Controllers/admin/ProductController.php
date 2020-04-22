<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Product;
use Auth;
// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Product\UpdateProductForm;

use DB;

class ProductController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $products = Product::all();
        return view('pages.company.product.index', compact('products'));
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
        return view('pages.company.product.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $form = $this->form(ProductForm::class);

            if (!$form->isValid()) {
                return redirect()->back()->withErrors($form->getErrors())->withInput();
            }
            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->city_id = Auth::user()->city_id;
            $product->company_id = Auth::id();
            $product->name = $request->name;
            $product->urdu_name = $request->urdu_name;
            $product->contract_price = $request->contract_price;
            $product->markeet_price = $request->markeet_price;
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
        return view('pages.company.product.create', compact('form'));
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

    public function setservice()
    {
        $user = User::find(1);
        $user->services()->sync([1,3]);
        $user->storelifter()->sync([2,3]);
        $service = Service::find(1);
        return $user->storelifter;
    }
}
