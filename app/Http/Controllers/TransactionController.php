<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Menu;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->level_id == 1) {
            $all = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->where('status', 'paid')
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $today = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->where('status', 'paid')
                                ->whereDate('created_at',Carbon::now())
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $thisMonth = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->where('status', 'paid')
                                ->whereMonth('created_at',Carbon::now()->month)
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
        } else {
            $all = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $today = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->whereDate('created_at',Carbon::now())
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $thisMonth = Transaction::with(['transaction_details', 'transaction_details.menu'])
                                ->whereMonth('created_at',Carbon::now()->month)
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
        }
    
        return view('transaction.index', [
            'all' => $all, 
            'today' => $today,
            'thisMonth' => $thisMonth
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Menu $menu)
    {
        $user = Auth::user();

        if ($user->level_id === 1) {
            return redirect()->back();
        }
        
        return view('transaction.create', [
            'foods' => $menu->where('category','food')->latest()->get(),
            'drinks' => $menu->where('category', 'drink')->latest()->get(),
            'tables' => Transaction::select('no_table')->where('status','unpaid')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_transaction' => 'required',
            'order_type' => 'required|in:dine_in,takeaway'
        ]);

        $transaction['total_transaction'] = $validated['total_transaction'];
        $transaction['order_type'] = $validated['order_type'];

        // Only require table if dine_in
        if ($request->order_type === 'dine_in') {
            $request->validate([
                'no_table' => 'required'
            ]);
            $transaction['no_table'] = $request->no_table;
        } else {
            $transaction['no_table'] = 'Bungkus';
        }

        $transaction['user_id'] = auth()->user()->id;
        $transaction['total_payment'] = 0;
        $transaction['status'] = 'unpaid';
        $transaction['created_at'] = Carbon::now();
        $transaction['updated_at'] = Carbon::now();

        $id = Transaction::insertGetId($transaction);

        $transactionDetail = $request->validate([
            'menu_id' => 'required'
        ]);

        $menu_id = json_decode($request->menu_id);

        for ($i=0; $i < count($menu_id); $i++) {
            $transactionDetail['transaction_id'] = $id;
            $transactionDetail['menu_id'] = $menu_id[$i]->menu_id;
            $transactionDetail['qty'] = $menu_id[$i]->qty;
            $transactionDetail['price'] = $menu_id[$i]->price;
            TransactionDetail::create($transactionDetail);
        };

        return redirect("/transaction/{$id}")->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        if ($user->level_id === 1) {
            return redirect()->back();
        }

        $data = Transaction::with(['transaction_details', 'transaction_details.menu', 'user'])
            ->where('id', $transaction->id)
            ->get();

        return view('transaction.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'total_transaction' => 'required|numeric',
            'total_payment' => 'required|numeric|gte:total_transaction',
            'payment_method' => 'required|in:cash,qris'
        ]);

        // Only update total_payment, payment_method and status, don't change total_transaction
        $updateData = [
            'total_payment' => filter_var($request->total_payment, FILTER_SANITIZE_NUMBER_INT),
            'payment_method' => $request->payment_method,
            'status' => 'paid'
        ];

        Transaction::where('id', $transaction->id)->update($updateData);

        return redirect("/transaction/{$transaction->id}")->with('success', 'Pembayaran Berhasil!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

   
}
