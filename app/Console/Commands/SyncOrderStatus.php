<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Services\ProviderPortalInterface;

class SyncOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync order status with provider portal';

    /**
     * Execute the console command.
     */
    public function handle(ProviderPortalInterface $provider)
    {
        $orders = Order::whereIn('status', ['ordered', 'processing'])->get();

        foreach ($orders as $order) {
            $data = $provider->getOrder($order->id);

            if ($data['status'] === $order->status) {
                continue;
            }

            $order->update(['status' => $data['status']]);
            $newStatus = $data['status'];
            $this->info("Updated status for order");
        }
    }
}
