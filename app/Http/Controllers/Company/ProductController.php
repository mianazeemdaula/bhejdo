<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Product;
use Auth;
// Forms
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Store\ProductForm;

use DB;

class ProductController extends Controller
{
    use FormBuilderTrait;
    public function index()
    {
        $products = Product::where('company_id',Auth::id())->get();
        return view('pages.company.product.index', compact('products'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $form = $this->form(ProductForm::class, [
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'url' => route('product.update', $id),
            'model' => $service
        ]);
        return view('pages.company.product.edit', compact('form'));
    }

    public function update(Request $request, Service $service)
    {
        $form = $this->form(ProductForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $service->s_name = $request->s_name;
        $service->s_price = $request->s_price;
        $service->s_status = $request->s_status;
        $service->urdu_name = $request->urdu_name;
        $service->cross_price = $request->cross_price;
        $service->lifter_price = $request->lifter_price;
        $service->description = $request->description;
        $service->scale = $request->scale;
        $service->min_qty_charges = $request->min_qty_charges;
        $service->max_qty = $request->max_qty;
        $service->min_qty = $request->min_qty;
        if($request->has('image')){
            $cover = $request->file('image');
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('services'), $imageName);
            //\Storage::disk('public')->put($imageName, $cover);
            $service->img_url = $imageName;
        }
        $service->save();
        return redirect()->back()->with('status', 'Service Updated!');
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
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('status', 'Exception Problem');
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
