<?php

namespace App\Http\Controllers\Api;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Products;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->paginate(10);

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->orderItems as $orderItem) {
                    $product = Products::where('id', $orderItem->product_id)->pluck('name');
                    $orderItem->product_name = $product[0];
                }
            }
            return response()->json([$orders], 200);
        } else return response()->json(['message' => 'Record not found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'oders_items' => 'required',
                'total_price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'date_delivery' => 'required',
            ]);

            $oders = new Order();
            $oders->user_id = Auth::user()->id;
            $oders->total_price = $request->total_price;
            $oders->date_delivery = $request->date_delivery;
            $oders->save();

            foreach ($request->oders_items as $oders_item) {
                $oders_item = new OrderItems();
                $oders_item->order_id = $oders->id;
                $oders_item->price = $oders_item['price'];
                $oders_item->product_id = $oders_item['product_id'];
                $oders_item->quantity = $oders_item['quantity'];
                $oders_item->save();

                $product = Products::find($oders_item->product_id);
                $product->quantity -= $oders_item->quantity;
                $product->save();
                // Set konfigurasi Midtrans
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = config('midtrans.is_sanitized');
                Config::$is3ds = config('midtrans.is_3ds');

                // Buat parameter transaksi
                $params = [
                    'transaction_details' => [
                        'order_id' => $oders->id,
                        'gross_amount' => $request->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ],
                    'item_details' => array_map(function ($item) {
                        return [
                            'id' => $item['product_id'],
                            'price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'name' => Products::find($item['product_id'])->name,
                        ];
                    }, $request->oders_items),
                ];
            }

            // Proses pembayaran
            $snapToken = Snap::getSnapToken($params);

            return response()->json(['snap_token' => $snapToken], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);
        return response()->json([$order], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function GetOrderItems(string $id)
    {
        $order_items = OrderItems::where('order_id', $id)->get();

        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Products::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product[0];
            }
            return response()->json([$order_items], 200);
        } else return response()->json(['message' => 'Record not found'], 404);
    }

    public function GetOrderItemsByUser()
    {
        $order_items = OrderItems::where('user_id', Auth::user()->id)->get();

        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Products::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product[0];
            }
            return response()->json([$order_items], 200);
        } else return response()->json(['message' => 'Record not found'], 404);
    }

    public function changeStatus(Request $request, string $id)
    {
        $order = Order::find($id);
        if (is_null($order)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Record successfully updated'], 200);
    }
}
