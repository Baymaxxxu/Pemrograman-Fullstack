<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 🔹 GET ALL DATA
    public function index()
    {
        return Order::with('items.product')->get();
    }

    // 🔹 GET DETAIL
    public function show($id)
    {
        return Order::with('items.product')->findOrFail($id);
    }

    // 🔥 TRANSAKSI KOMPLEKS (CREATE ORDER)
    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'customer_name' => 'required|string',
            'items' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            // buat order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'total_price' => 0
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // cek stok
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stock {$product->name} tidak cukup");
                }

                // kurangi stok
                $product->decrement('stock', $item['qty']);

                $subtotal = $product->price * $item['qty'];

                // simpan item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $subtotal
                ]);

                $total += $subtotal;
            }

            // update total harga
            $order->update([
                'total_price' => $total
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 UPDATE
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'customer_name' => $request->customer_name
        ]);

        return response()->json([
            'message' => 'Order berhasil diupdate'
        ]);
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        Order::destroy($id);

        return response()->json([
            'message' => 'Order berhasil dihapus'
        ]);
    }
}