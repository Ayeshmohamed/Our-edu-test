<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTransactionsController extends Controller
{
    use ResponseTrait;
    // Migrate Data From Json Files
    public function migrate()
    {
        try {
            // Reed Users File & Create Users
            if (file_exists(base_path('users.json'))) {
                $get_users = file_get_contents(base_path('users.json'));
                $users = json_decode($get_users, true);
                foreach ($users['users'] as $user) {
                    User::firstOrcreate($user);
                }
            }

            // Read Transactions & Create Transactions
            if (file_exists(base_path('transactions.json'))) {
                $get_transactions = file_get_contents(base_path('transactions.json'));
                $transactions = json_decode($get_transactions, true);
                foreach ($transactions['transactions'] as $transaction) {
                    Transaction::firstOrcreate($transaction);
                }
            }

            return $this->json_response(null, true, 'migrated successfully');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->json_response(null, true, $th->getMessage());
        }
    } //Migrate Data Function


    // List Transactions With User Start
    public function transactions_list(Request $request)
    {
        $transactions =  new Transaction();

        // filter tansaction with statusCode array
        if ($request->statusCodes) {
            $transactions = $transactions->whereIn('statusCode', $request->statusCodes);
        }

        // filter tansaction with Currency array
        if ($request->currencies) {
            $transactions = $transactions->whereIn('Currency', $request->currencies);
        }

            // filter by amount range if both [from , to] and only [from] or only [to]
            if ($request->amount_range) {
                $transactions = $transactions->where(function ($query)  use ($request) {
                    if (isset($request->amount_range['from']) && isset($request->amount_range['to'])) {
                        $query->where('paidAmount', '>=', $request->amount_range['from'])->where('paidAmount', '<=', $request->amount_range['to']);
                    } else if (isset($request->amount_range['from']) && !isset($request->amount_range['to'])) {
                        $query = $query->where('paidAmount', '>=', $request->amount_range['from']);
                    } else if (isset($request->amount_range['to']) && !isset($request->amount_range['from'])) {
                        $query = $query->where('paidAmount', '<=', $request->amount_range['to']);
                    }
                });
            }

            // filter by date range if both [from , to] and only [from] or only [to]
            if ($request->date_range) {
                $transactions = $transactions->where(function ($query) use ($request) {
                    if (isset($request->date_range['from']) && isset($request->date_range['to'])) {
                        $query = $query->whereBetween('paymentDate', [date('Y-m-d', strtotime($request->date_range['from'])), date('Y-m-d', strtotime($request->date_range['to']))]);
                    } else if (isset($request->date_range['from']) && !isset($request->date_range['to'])) {
                        $query = $query->whereDate('paymentDate', '>=', date('Y-m-d', strtotime($request->date_range['from'])));
                    } else if (isset($request->date_range['to']) && !isset($request->date_range['from'])) {
                        $query = $query->whereDate('paymentDate', '<=', date('Y-m-d', strtotime($request->date_range['to'])));
                    }
                });
            }

        // Get transaction eager loading user
        $transactions = $transactions->with('user')->get();

        // Transform data
        $transactions = TransactionResource::collection($transactions);

        return $this->json_response($transactions, true, 'user Transactions successfully');
    } // List Transactions With User End

    // List Users With Transactions Start
    public function users_list(Request $request)
    {
        $users =  new User();

        $users = $users->whereHas('transactions', function ($transactions) use ($request) {
            // filter tansaction with statusCode array
            if ($request->statusCodes) {
                $transactions = $transactions->whereIn('statusCode', $request->statusCodes);
            }

            // filter tansaction with Currency array
            if ($request->currencies) {
                $transactions = $transactions->whereIn('Currency', $request->currencies);
            }

            // filter by amount range if both [from , to] and only [from] or only [to]
            if ($request->amount_range) {
                $transactions = $transactions->where(function ($query)  use ($request) {
                    if (isset($request->amount_range['from']) && isset($request->amount_range['to'])) {
                        $query->where('paidAmount', '>=', $request->amount_range['from'])->where('paidAmount', '<=', $request->amount_range['to']);
                    } else if (isset($request->amount_range['from']) && !isset($request->amount_range['to'])) {
                        $query = $query->where('paidAmount', '>=', $request->amount_range['from']);
                    } else if (isset($request->amount_range['to']) && !isset($request->amount_range['from'])) {
                        $query = $query->where('paidAmount', '<=', $request->amount_range['to']);
                    }
                });
            }

            // filter by date range if both [from , to] and only [from] or only [to]
            if ($request->date_range) {
                $transactions = $transactions->where(function ($query) use ($request) {
                    if (isset($request->date_range['from']) && isset($request->date_range['to'])) {
                        $query = $query->whereBetween('paymentDate', [date('Y-m-d', strtotime($request->date_range['from'])), date('Y-m-d', strtotime($request->date_range['to']))]);
                    } else if (isset($request->date_range['from']) && !isset($request->date_range['to'])) {
                        $query = $query->whereDate('paymentDate', '>=', date('Y-m-d', strtotime($request->date_range['from'])));
                    } else if (isset($request->date_range['to']) && !isset($request->date_range['from'])) {
                        $query = $query->whereDate('paymentDate', '<=', date('Y-m-d', strtotime($request->date_range['to'])));
                    }
                });
            }
        });


        $users = $users->with('transactions')->get();

        $transactions = UserResource::collection($users);
        return $this->json_response($transactions, true, 'user Transactions successfully');
    }    // List Users With Transactions End

}
