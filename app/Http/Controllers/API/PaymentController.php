<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\User;
use App\Models\History;
use App\Models\Payment;
use App\Http\Resources\UserResource as UserResource;
use App\Http\Resources\PaymentResource as PaymentResource;
use App\Http\Resources\PaymentCollection as PaymentCollection;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class PaymentController extends BaseController
{
    public function index(Request $request)
    {   
        $payments = DB::table('payments')
                    ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                    ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                    ->groupBy('payments.id')
                    ->orderBy('payments.tanggal','DESC')
                    ->paginate(5);
                    
        return (new PaymentCollection($payments))
                ->additional(['meta' => [
                    'key' => 'value',
                ]]);
    }
    public function store(Request $request)
    {   
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'date' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);
        

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
        $payment = new Payment();
        $dataPayment = $this->setPaymentData($payment, $input);
        return $this->sendResponse($dataPayment, 'Successfully created new payment data.');
    }

   
    public function show($id)
    {
        $payments = DB::table('payments')
                    ->where('payments.id','=',$id)
                    ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                    ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                    ->groupBy('payments.id')
                    ->first();
        
        if (is_null($payments)) {
            return $this->sendError('Data does not exist.');
        }
        
        return $this->sendResponse($payments, 'data payment fetched');
    }
    

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'date' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $this->setPaymentData($payment, $input);
        
        return $this->sendResponse($payment, 'Data payment updated.');
    }
    
    private function setPaymentData($dataPayment, $data)
    {
        $dataPayment->title        = $data['title'];
        $dataPayment->tanggal      = $data['date'];
        $dataPayment->type         = $data['type'];
        $dataPayment->keterangan   = $data['description'];
        $dataPayment->save();

        return $dataPayment;
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);
        $payment->delete();
        return $this->sendResponse($payment, 'Payment data deleted.');
    }
}
