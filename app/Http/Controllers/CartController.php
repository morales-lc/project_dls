<?php

namespace App\Http\Controllers;

use App\Models\AlertBook;
use App\Models\CartItem;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller as BaseController;

class CartController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;

        if (! $this->isStudentOrFaculty($user) || ! $sf) {
            return view('carts.index', ['cartItems' => collect()]);
        }

        $cartItems = CartItem::with('cartable')
            ->where('student_faculty_id', $sf->id)
            ->latest()
            ->get();

        return view('carts.index', compact('cartItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;
        if (! $this->isStudentOrFaculty($user) || ! $sf) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only students and faculty can use My Cart.'
                ], 403);
            }
            return back()->with('error', 'Only students and faculty can use My Cart.');
        }

        $map = [
            'catalog' => Catalog::class,
            'alert_book' => AlertBook::class,
        ];

        $type = $request->input('type');
        if (! isset($map[$type])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid cart item type.'
                ], 422);
            }
            return back()->with('error', 'Invalid cart item type.');
        }

        $modelClass = $map[$type];
        $item = $modelClass::find($request->integer('id'));
        if (! $item) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item not found.'
                ], 404);
            }
            return back()->with('error', 'Item not found.');
        }

        $existing = CartItem::where('student_faculty_id', $sf->id)
            ->where('cartable_id', $item->id)
            ->where('cartable_type', $modelClass)
            ->first();

        if ($existing) {
            $existing->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'removed', 'message' => 'Removed from My Cart.']);
            }
            return back()->with('success', 'Removed from My Cart.');
        }

        CartItem::create([
            'student_faculty_id' => $sf->id,
            'cartable_id' => $item->id,
            'cartable_type' => $modelClass,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'added', 'message' => 'Added to My Cart.']);
        }

        return back()->with('success', 'Added to My Cart.');
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;

        if (! $this->isStudentOrFaculty($user) || ! $sf) {
            return redirect()->route('dashboard')->with('error', 'Only students and faculty can checkout from My Cart.');
        }

        $cartItems = CartItem::with('cartable')
            ->where('student_faculty_id', $sf->id)
            ->latest()
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $entries = [];
        foreach ($cartItems as $cartItem) {
            if ($cartItem->cartable_type !== Catalog::class) {
                continue;
            }

            $item = $cartItem->cartable;
            if (! $item) {
                continue;
            }

            $title = trim((string) ($item->title ?? 'Untitled'));
            $author = trim((string) ($item->author ?? ''));
            $callNumber = trim((string) ($item->call_number ?? ''));
            if ($title === '' && $author === '' && $callNumber === '') {
                continue;
            }

            $entries[] = [
                'cart_item_id' => (int) $cartItem->id,
                'catalog_id' => (int) $item->id,
                'title' => $title,
                'author' => $author,
                'call_number' => $callNumber,
            ];
        }

        if (empty($entries)) {
            return redirect()->route('cart.index')->with('error', 'No valid catalog items to checkout.');
        }

        $checkoutToken = Str::random(48);
        session()->put('lira_cart_checkout_maps.' . $checkoutToken, $entries);

        return redirect()->route('lira.form', [
            'action' => 'borrow',
            'from_cart' => 1,
            'checkout_token' => $checkoutToken,
            'return_to' => route('cart.index'),
        ]);
    }

    private function isStudentOrFaculty($user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array($user->role, ['student', 'faculty'], true);
    }
}
