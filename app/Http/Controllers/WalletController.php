<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bonus;
use App\Wallet;
use App\User;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Wallet\WalletForm;


class WalletController extends Controller
{
    use FormBuilderTrait;
    public function index($id)
    {
        $user = User::find($id);
        $collection = $user->wallet()->latest()->get();
        return view('pages.admin.wallet.index', compact('collection', 'user'));
    }

    public function create($id)
    {
        $form = $this->form(WalletForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route("user.wallet.store", [$id]),
        ]);
        return view('pages.admin.wallet.create', compact('form'));
    }

    public function store(Request $request, $id)
    {
        $form = $this->form(WalletForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        try{
            DB::beginTransaction();
            if($request->amount > 0){
                Wallet::add($id,$request->description,'topup',$request->amount);
            }else{
                Wallet::deduct($id,$request->description,'transfer',$request->amount);
            }
            DB::commit();
            return redirect()->back()->with('status', ['Wallet entry save successfully', $patient->id]);
        }catch(Expection $ex){
            DB::rollBack();
            return redirect()->back()->with('error', $ex);
        }
    }
}
