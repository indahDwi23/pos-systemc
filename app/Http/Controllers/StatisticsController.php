<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', '7days'); // today, 7days, 30days, 90days, all

        try {
            // Determine date range based on filter
            $startDate = match($filter) {
                'today' => Carbon::today(),
                '7days' => Carbon::now()->subDays(7),
                '30days' => Carbon::now()->subDays(30),
                '90days' => Carbon::now()->subDays(90),
                'all' => Carbon::createFromFormat('Y-m-d', '2020-01-01'),
                default => Carbon::now()->subDays(7),
            };

            // Get daily sales for the selected range
            $days = $filter === 'today' ? 1 : ($filter === '7days' ? 7 : ($filter === '30days' ? 30 : ($filter === '90days' ? 90 : 7)));

            $dailySales = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_transaction) as total')
            )
                ->where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Fill in missing dates with 0
            $chartData = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $sales = $dailySales->firstWhere('date', $date);
                $chartData[] = [
                    'date' => Carbon::parse($date)->locale('id')->translatedFormat('D'),
                    'total' => $sales ? (int) $sales->total : 0
                ];
            }

            // Get top selling menu items for the selected range
            $topMenuItems = TransactionDetail::select('menu_id', DB::raw('SUM(qty) as total_qty'))
                ->whereHas('transaction', function ($query) use ($startDate) {
                    $query->where('status', 'paid')->where('created_at', '>=', $startDate);
                })
                ->with('menu:id,name')
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            // Get sales by payment method for the selected range
            $salesByPaymentMethod = Transaction::select(
                    DB::raw('COALESCE(payment_method, "cash") as payment_method'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total_transaction) as total')
                )
                ->where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('COALESCE(payment_method, "cash")'))
                ->get();

            // Calculate total sales and orders for the selected range
            $totalSales = Transaction::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('total_transaction') ?? 0;

            $totalOrders = Transaction::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->count();

            // Today's summary (always show today regardless of filter)
            $todaySales = Transaction::where('status', 'paid')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_transaction') ?? 0;

            $todayOrders = Transaction::where('status', 'paid')
                ->whereDate('created_at', Carbon::today())
                ->count();

            return view('statistics.index', compact(
                'filter',
                'chartData',
                'topMenuItems',
                'salesByPaymentMethod',
                'totalSales',
                'totalOrders',
                'todaySales',
                'todayOrders'
            ));
        } catch (\Exception $e) {
            // Return empty data on error
            return view('statistics.index', [
                'filter' => $filter,
                'chartData' => [],
                'topMenuItems' => collect(),
                'salesByPaymentMethod' => collect(),
                'totalSales' => 0,
                'totalOrders' => 0,
                'todaySales' => 0,
                'todayOrders' => 0
            ]);
        }
    }

    public function profit(Request $request)
    {
        $filter = $request->get('filter', '7days'); // today, 7days, 30days, 90days, all

        try {
            // Determine date range based on filter
            $startDate = match($filter) {
                'today' => Carbon::today(),
                '7days' => Carbon::now()->subDays(7),
                '30days' => Carbon::now()->subDays(30),
                '90days' => Carbon::now()->subDays(90),
                'all' => Carbon::createFromFormat('Y-m-d', '2020-01-01'),
                default => Carbon::now()->subDays(7),
            };

            // Get days for chart
            $days = $filter === 'today' ? 1 : ($filter === '7days' ? 7 : ($filter === '30days' ? 30 : ($filter === '90days' ? 90 : 7)));

            // Get daily profit for the selected range
            $dailyProfit = TransactionDetail::select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM((menus.price - menus.modal) * transaction_details.qty) as total_profit')
            )
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->where('transactions.status', 'paid')
                ->where('transactions.created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(transactions.created_at)'))
                ->orderBy('date')
                ->get();

            // Fill in missing dates with 0
            $chartData = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $profit = $dailyProfit->firstWhere('date', $date);
                $chartData[] = [
                    'date' => Carbon::parse($date)->locale('id')->translatedFormat('D'),
                    'total' => $profit ? (int) $profit->total_profit : 0
                ];
            }

            // Get top profitable menu items for the selected range
            $topProfitableItems = TransactionDetail::select(
                'menu_id',
                DB::raw('SUM((menus.price - menus.modal) * transaction_details.qty) as total_profit'),
                DB::raw('SUM(transaction_details.qty) as total_qty')
            )
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->where('transactions.status', 'paid')
                ->where('transactions.created_at', '>=', $startDate)
                ->groupBy('menu_id')
                ->orderByDesc('total_profit')
                ->limit(5)
                ->with('menu:id,name')
                ->get();

            // Calculate total profit for the selected range
            $totalProfit = TransactionDetail::select(
                DB::raw('SUM((menus.price - menus.modal) * transaction_details.qty) as total')
            )
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->where('transactions.status', 'paid')
                ->where('transactions.created_at', '>=', $startDate)
                ->value('total') ?? 0;

            // Get total sales for comparison
            $totalSales = Transaction::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('total_transaction') ?? 0;

            // Calculate profit margin
            $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales * 100) : 0;

            // Get profit by payment method
            $profitByPaymentMethod = TransactionDetail::select(
                DB::raw('COALESCE(transactions.payment_method, "cash") as payment_method'),
                DB::raw('SUM((menus.price - menus.modal) * transaction_details.qty) as total_profit')
            )
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->where('transactions.status', 'paid')
                ->where('transactions.created_at', '>=', $startDate)
                ->groupBy(DB::raw('COALESCE(transactions.payment_method, "cash")'))
                ->get()
                ->keyBy('payment_method');

            $cashProfit = $profitByPaymentMethod->get('cash')?->total_profit ?? 0;
            $qrisProfit = $profitByPaymentMethod->get('qris')?->total_profit ?? 0;

            // Today's profit summary (always show today regardless of filter)
            $todayProfit = TransactionDetail::select(
                DB::raw('SUM((menus.price - menus.modal) * transaction_details.qty) as total')
            )
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->where('transactions.status', 'paid')
                ->whereDate('transactions.created_at', Carbon::today())
                ->value('total') ?? 0;

            return view('statistics.profit', compact(
                'filter',
                'chartData',
                'topProfitableItems',
                'totalProfit',
                'totalSales',
                'profitMargin',
                'todayProfit',
                'cashProfit',
                'qrisProfit'
            ));
        } catch (\Exception $e) {
            // Return empty data on error
            return view('statistics.profit', [
                'filter' => $filter,
                'chartData' => [],
                'topProfitableItems' => collect(),
                'totalProfit' => 0,
                'totalSales' => 0,
                'profitMargin' => 0,
                'todayProfit' => 0,
                'cashProfit' => 0,
                'qrisProfit' => 0
            ]);
        }
    }
}
