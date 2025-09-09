<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $orders = Order::with(['user', 'orderItems', 'transaction'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'locality' => 'nullable|string|max:255',
                'address' => 'required|string',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'zip' => 'required|string|max:10',
                'type' => 'required|in:home,office',
                'status' => 'sometimes|in:ordered,delivered,cancelled',
                'is_shipping_different' => 'boolean',
                'delivered_date' => 'nullable|date',
                'cancelled_date' => 'nullable|date'
            ]);

            $order = Order::create($validated);
            $order->load(['user', 'orderItems', 'transaction']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $order = Order::with(['user', 'orderItems', 'transaction'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'sometimes|required|exists:users,id',
                'subtotal' => 'sometimes|required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'total' => 'sometimes|required|numeric|min:0',
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'locality' => 'nullable|string|max:255',
                'address' => 'sometimes|required|string',
                'city' => 'sometimes|required|string|max:255',
                'state' => 'sometimes|required|string|max:255',
                'country' => 'sometimes|required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'zip' => 'sometimes|required|string|max:10',
                'type' => 'sometimes|required|in:home,office',
                'status' => 'sometimes|in:ordered,delivered,cancelled',
                'is_shipping_different' => 'boolean',
                'delivered_date' => 'nullable|date',
                'cancelled_date' => 'nullable|date'
            ]);

            // Check if trying to set status to 'delivered' and validate product quantities
            if (isset($validated['status']) && $validated['status'] === 'delivered') {
                $orderItems = OrderItem::where('order_id', $order->id)->with('product')->get();
                $outOfStockProducts = [];
                
                foreach ($orderItems as $item) {
                    if ($item->product && $item->product->quantity == 0) {
                        $outOfStockProducts[] = $item->product->name;
                    }
                }
                
                if (!empty($outOfStockProducts)) {
                    $productList = implode(', ', $outOfStockProducts);
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot mark order as delivered. The following products have zero quantity: {$productList}. Please add stock before marking as delivered.",
                        'out_of_stock_products' => $outOfStockProducts
                    ], 400);
                }
            }

            $order->update($validated);
            $order->load(['user', 'orderItems', 'transaction']);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            
            // Check if order has items or transaction
            if ($order->orderItems()->count() > 0 || $order->transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete order that has items or transactions'
                ], 409);
            }
            
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order's items
     */
    public function getOrderItems(string $id): JsonResponse
    {
        try {
            $order = Order::with('orderItems.product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order->orderItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Get order's transaction
     */
    public function getTransaction(string $id): JsonResponse
    {
        try {
            $order = Order::with('transaction')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order->transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
}
