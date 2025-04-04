<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ProviderPortalInterface;

class OrderController extends Controller
{
    protected ProviderPortalInterface $provider;

    public function __construct(ProviderPortalInterface $provider)
    {
        $this->provider = $provider;
    }

    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->has('name')) {
            $name = '%' . $request->name . '%';
            $query->where('name', 'LIKE', $name);
        }

        if ($request->has('sort')) {
            $query->orderBy($request->sort);
        }

        return response()->json($query->get(['id', 'name', 'type', 'status']));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string',
                'type' => 'required|in:connector,vpn_connection',
            ]
        );

        /** @var \App\Services\ProviderClientInterface $providerClient */
        $providerClient = app()->make(\App\Services\ProviderClientInterface::class);
        $providerOrder = $providerClient->createOrder($request->type);

        $order = Order::create(
            [
                'id' => Str::uuid(),
                'name' => \trim($request->name),
                'type' => $providerOrder['type'],
                'status' => $providerOrder['status'],
                'provider_order_id' => $providerOrder['id'],
            ]
        );

        return response()->json([
            'id' => $order->id,
            'name' => $order->name,
            'type' => $order->type,
            'status' => $order->status,
        ], 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order->only('id', 'name', 'type', 'status'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'completed') {
            return response()->json(['error' => 'Order is not completed and can not be deleted.'], 400);
        }

        $this->provider->deleteOrder($order->id);
        $order->delete();

        return response()->json(['message' => 'Order successfully deleted.']);
    }
}
