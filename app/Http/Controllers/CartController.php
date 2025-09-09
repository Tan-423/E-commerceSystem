<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\CartInterface;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $cart;

    public function __construct(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cartItems = $this->cart->getItems();
        return view('cart', compact('cartItems'));
    }

    public function add_to_cart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'id' => 'required|integer|exists:products,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $options = [];
        if ($request->has('selected_image')) {
            $options['selected_image'] = $request->selected_image;
        }

        try {
            $this->cart->addItem(
                $request->id,
                strip_tags($request->name),
                $request->quantity,
                $request->price,
                $options
            );

            $removedFromWishlist = $this->removeFromWishlistAfterAddingToCart($request->id);

            $message = 'Product added to cart successfully!';
            if ($removedFromWishlist) {
                $message .= ' Item has been removed from your wishlist.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add product to cart: ' . $e->getMessage());
        }
    }

    private function removeFromWishlistAfterAddingToCart($productId)
    {
        if (Auth::check()) {
            $deleted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();
            return $deleted > 0;
        }
        return false;
    }

    public function increase_cart_quantity($rowId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = $this->cart->getItems()->get($rowId);
        $this->cart->updateItem($rowId, $item->qty + 1);

        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = $this->cart->getItems()->get($rowId);
        $this->cart->updateItem($rowId, $item->qty - 1);

        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->cart->removeItem($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->cart->clear();
        return redirect()->back();
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::id())
            ->where('isdefault', 1)
            ->first();

        return view('checkout', compact('address'));
    }

    public function setAmountForCheckout()
    {
        if (!($this->cart->count() > 0)) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => floatval(str_replace(',', '', Session::get('discounts')['discount'])),
                'subtotal' => floatval(str_replace(',', '', Session::get('discounts')['subtotal'])),
                'tax'      => floatval(str_replace(',', '', Session::get('discounts')['tax'])),
                'total'    => floatval(str_replace(',', '', Session::get('discounts')['total'])),
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => $this->cart->subtotal(),
                'tax'      => $this->cart->tax(),
                'total'    => $this->cart->total(),
            ]);
        }
    }

    public function place_order(Request $request)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            return redirect()->route('login')->with('error', 'Please login to place an order.');
        }

        if ($this->cart->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before placing an order.');
        }

        $validationRules = [
            'name'     => 'required|string|max:100|min:2',
            'phone'    => 'required|numeric|digits_between:9,15',
            'zip'      => 'required|numeric|digits_between:4,10',
            'state'    => 'required|string|max:50|min:2|regex:/^[a-zA-Z\s\-\.]+$/',
            'city'     => 'required|string|max:50|min:2|regex:/^[a-zA-Z\s\-\.]+$/',
            'address'  => 'required|string|max:200|min:5',
            'locality' => 'required|string|max:100|min:2',
            'landmark' => 'required|string|max:100|min:2',
            'mode'     => 'required|in:cod'
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $address = Address::where('user_id', $user_id)
            ->where('isdefault', true)
            ->first();

        if ($address) {
            $address->update([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'zip'      => $request->zip,
                'state'    => $request->state,
                'city'     => $request->city,
                'address'  => $request->address,
                'locality' => $request->locality,
                'landmark' => $request->landmark,
                'country'  => 'MALAYSIA',
            ]);
        } else {
            $address = Address::create([
                'user_id'  => $user_id,
                'name'     => $request->name,
                'phone'    => $request->phone,
                'zip'      => $request->zip,
                'state'    => $request->state,
                'city'     => $request->city,
                'address'  => $request->address,
                'locality' => $request->locality,
                'landmark' => $request->landmark,
                'country'  => 'MALAYSIA',
                'isdefault' => true,
            ]);
        }

        $this->setAmountForCheckout();
        $checkout = Session::get('checkout');

        if (!$checkout || !isset($checkout['total']) || $checkout['total'] <= 0) {
            return redirect()->route('cart.checkout')
                ->with('error', 'Invalid order total. Please try again.');
        }

        try {
            $order = new Order();
            $order->user_id  = $user_id;
            $order->subtotal = $checkout['subtotal'];
            $order->discount = $checkout['discount'];
            $order->tax      = $checkout['tax'];
            $order->total    = $checkout['total'];
            $order->name     = $address->name;
            $order->phone    = $address->phone;
            $order->locality = $address->locality;
            $order->address  = $address->address;
            $order->city     = $address->city;
            $order->state    = $address->state;
            $order->country  = $address->country;
            $order->landmark = $address->landmark;
            $order->zip      = $address->zip;
            $order->save();
        } catch (\Exception $e) {
            return redirect()->route('cart.checkout')
                ->with('error', 'Failed to create order. Please try again.');
        }

        try {
            foreach ($this->cart->getItems() as $item) {
                $orderitem = new OrderItem();
                $orderitem->product_id = $item->id;
                $orderitem->order_id   = $order->id;
                $orderitem->price      = $item->price;
                $orderitem->quantity   = $item->qty;
                $orderitem->save();
            }

            $transaction = new Transaction();
            $transaction->user_id  = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode     = $request->mode;
            $transaction->status   = "pending";
            $transaction->save();
        } catch (\Exception $e) {
            $order->delete();
            return redirect()->route('cart.checkout')
                ->with('error', 'Failed to process order. Please try again.');
        }

        $this->cart->clear();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');

        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }

    public function order_confirmation()
    {
        $order_id = Session::get('order_id');
        $order = Order::find($order_id);

        return view('order-confirmation', compact('order'));
    }
}
