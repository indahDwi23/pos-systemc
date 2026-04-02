<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate actual profit from transaction details
        $transactions = Transaction::with('transaction_details.menu')
            ->whereDate('created_at', NOW()->toDateString())
            ->where('status', 'paid')
            ->get();

        $totalProfit = 0;
        foreach ($transactions as $transaction) {
            foreach ($transaction->transaction_details as $detail) {
                $modal = $detail->menu->modal ?? 0;
                $price = $detail->menu->price ?? 0;
                $totalProfit += ($price - $modal) * $detail->qty;
            }
        }

        return view('home', [
            "total_menus" => Menu::all()->count(),
            'total_sales' => Transaction::select(Transaction::raw('SUM(total_transaction) as total_sales'))->whereDate('created_at', NOW()->toDateString())->get(),
            'total_income' => [(object)['total_income' => $totalProfit]],
            'invoice' => Transaction::select(Transaction::raw('COUNT(id) as total_invoice'))->whereDate('created_at', NOW()->toDateString())->get(),
            'cashier' => User::select(User::raw('COUNT(id) as cashier'))->where('level_id', 2)->get(),
            'owner' => User::select(User::raw('COUNT(id) as owner'))->where('level_id', 1)->get(),
            'total_user' => User::select(User::raw('COUNT(id) as total_user'))->get(),
            'total_paid' => Transaction::select(Transaction::raw('COUNT(id) as total_paid'))->where('status','paid')->get(),
            'total_unpaid' => Transaction::select(Transaction::raw('COUNT(id) as total_unpaid'))->where('status','unpaid')->get(),
            'tables' => Transaction::select(Transaction::raw('COUNT(no_table) as tables'))->where('status','unpaid')->get()
        ]);
    }
}
