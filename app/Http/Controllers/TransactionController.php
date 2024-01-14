<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\TransactionStatus;

class TransactionController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            $transaction = Transaction::all();
        } else {
            $transaction = auth()->user()->transactions()->get();
        }
        return response([
            'transaction' =>
            TransactionResource::collection($transaction),
            'message' => 'Successful'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_transaction(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount' => 'required',
            'payer' => 'required',
            'due_on' => 'required',
            'vat' => 'required',
            'is_vat_inclusive' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'Validation Error'
            ]);
        }

        $data['status'] = 'outstanding';

        $transaction = Transaction::create($data);

        return response([
            'transaction' => new
                TransactionResource($transaction),
            'message' => 'Success'
        ], 200);
    }

    public function store_transaction_status(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount' => 'required',
            'paid_on' => 'required',
            'transaction_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'Validation Error'
            ]);
        }

        $transaction_statuses = TransactionStatus::where('transaction_id', $data['transaction_id'])->get()->toArray();
        // dd($transaction_statuses);
        $total_paid = $data['amount'];
        foreach ($transaction_statuses as $transaction_status) {
            if ($transaction_status['transaction_id'] == $data['transaction_id']) {
                $total_paid = $total_paid + $transaction_status['amount'];
            }
        }

        $transaction = Transaction::where('id', $data['transaction_id'])->first();
        if ($total_paid == $transaction->amount) {
            $transaction->status = 'paid';
        } else {
            $due_on = new DateTime($transaction->due_on);
            $paid_on = new DateTime($data['paid_on']);
            if ($paid_on > $due_on) {
                $transaction->status = 'overdue';
            } else {
                $transaction->status = 'outstanding';
            }
        }

        $transaction_status['transaction_id'] = $data['transaction_id'];
        $transaction_status['amount'] = $data['amount'];
        $transaction_status['paid_on'] = $data['paid_on'];
        if (!empty($data['details'])) {
            $transaction_status['details'] = $data['details'];
        }
        TransactionStatus::create($transaction_status);

        $transaction->update($transaction->toArray());

        return response([
            'transaction' => new
                TransactionResource($transaction),
            'message' => 'Success'
        ], 200);
    }
}
