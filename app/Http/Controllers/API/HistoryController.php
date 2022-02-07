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
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class HistoryController extends BaseController
{
    public function index()
    {
        $dataHistory = History::all();
        return $this->sendResponse($dataHistory, 'User data fetched.');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
            'payment_id' => 'required',
            'total' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $history = new History();
        $dataHistory = $this->setHistoryData($history, $input);
        return $this->sendResponse($dataHistory, 'Hisory data Created');
    }

    public function showByPaymentID($id)
    {
        $payments = DB::table('histories')
                    ->leftJoin('payments', 'histories.payment_id', '=', 'payments.id')
                    ->leftJoin('users', 'histories.user_id', '=', 'users.id')
                    ->select('*','histories.id as id')
                    ->where('payment_id','=',$id)
                    ->get();
        
        if (is_null($payments)) {
            return $this->sendError('Data does not exist.');
        }
        
        return $this->sendResponse($payments, 'data payment fetched');
    }

    public function update(Request $request, $id)
    {
        $history = History::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'user_id' => 'required',
            'payment_id' => 'required',
            'total' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $this->setHistoryData($history, $input);
        
        return $this->sendResponse($history, 'Data history updated.');
    }

    private function setHistoryData($dataHistory, $data)
    {
        $dataHistory->user_id           = $data['user_id'];
        $dataHistory->payment_id        = $data['payment_id'];
        $dataHistory->total             = $data['total'];
        if(isset($data['keterangan'])){
            $dataHistory->description     = $data['keterangan'];
        }
        $dataHistory->save();

        return $dataHistory;
    }

    public function getIncome(){
        $payments = DB::table('payments')
                    ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                    ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                    ->groupBy('payments.id')
                    ->where('type','=','income')
                    
                    ->get();
        
        $payments = $payments->sum('total');

        return $this->sendResponse($payments, 'Successfully get data.');
    }

    public function getOutcome(){
        $payments = DB::table('payments')
                    ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                    ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                    ->groupBy('payments.id')
                    ->where('type','=','outcome')
                    
                    ->get();
        
        $payments = $payments->sum('total');

        return $this->sendResponse($payments, 'Successfully get data.');
    }

    public function getSaldo(){
        $payments_income = DB::table('payments')
                            ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                            ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                            ->groupBy('payments.id')
                            ->where('type','=','income')
                            ->get();
        
        $payments_income = $payments_income->sum('total');

        $payments_outcome = DB::table('payments')
                            ->leftJoin('histories', 'payments.id', '=', 'histories.payment_id')
                            ->select('payments.*', 'histories.payment_id', DB::raw('SUM(histories.total) as total'))
                            ->groupBy('payments.id')
                            ->where('type','=','outcome')
                            ->get();
        
        $payments_outcome = $payments_outcome->sum('total');

        $payments_saldo = $payments_income - $payments_outcome;

        return $this->sendResponse($payments_saldo, 'Successfully get data.');
    }

    public function destroy($id)
    {
        $history = History::find($id);
        $history->delete();
        return $this->sendResponse($history, 'Data History with id '.$id.' deleted.');
    }
}
