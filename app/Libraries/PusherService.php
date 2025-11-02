<?php

namespace App\Libraries;

use Config\Pusher as PusherConfig;
use Pusher\Pusher;

/**
 * PusherService
 * 
 * Service class untuk handle Pusher WebSocket broadcasting
 */
class PusherService
{
    protected ?Pusher $pusher = null;
    protected PusherConfig $config;

    public function __construct()
    {
        $this->config = config('Pusher');
        $this->initializePusher();
    }

    /**
     * Initialize Pusher instance
     */
    protected function initializePusher(): void
    {
        if (empty($this->config->appKey) || empty($this->config->appSecret)) {
            log_message('error', 'Pusher credentials not configured');
            return;
        }

        try {
            $this->pusher = new Pusher(
                $this->config->appKey,
                $this->config->appSecret,
                $this->config->appId,
                [
                    'cluster' => $this->config->appCluster,
                    'useTLS'  => $this->config->useTLS,
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Failed to initialize Pusher: ' . $e->getMessage());
        }
    }

    /**
     * Broadcast stock update event
     * 
     * @param int    $outletId   ID outlet yang stock-nya diupdate
     * @param int    $productId  ID product yang diupdate
     * @param int    $newStock   Jumlah stock baru
     * @param string $productName Nama product
     * @return bool
     */
    public function broadcastStockUpdate(int $outletId, int $productId, int $newStock, string $productName = ''): bool
    {
        if ($this->pusher === null) {
            return false;
        }

        try {
            $data = [
                'outlet_id'    => $outletId,
                'product_id'   => $productId,
                'new_stock'    => $newStock,
                'product_name' => $productName,
                'timestamp'    => date('Y-m-d H:i:s'),
            ];

            // Channel: stock-updates-{outlet_id}
            // Event: stock-updated
            $channel = "stock-updates-{$outletId}";
            
            $this->pusher->trigger($channel, 'stock-updated', $data);

            log_message('info', "Stock update broadcasted: Product #{$productId} at Outlet #{$outletId} - New stock: {$newStock}");

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to broadcast stock update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast new transaction event
     * 
     * @param int    $outletId          ID outlet
     * @param int    $transactionId     ID transaksi
     * @param string $transactionNumber Nomor transaksi
     * @param float  $total             Total transaksi
     * @param string $paymentMethod     Metode pembayaran
     * @param string $cashierName       Nama kasir
     * @param string $customerName      Nama customer (optional)
     * @return bool
     */
    public function broadcastNewTransaction(
        int $outletId,
        int $transactionId,
        string $transactionNumber,
        float $total,
        string $paymentMethod,
        string $cashierName,
        string $customerName = ''
    ): bool {
        if ($this->pusher === null) {
            return false;
        }

        try {
            $data = [
                'outlet_id'          => $outletId,
                'transaction_id'     => $transactionId,
                'transaction_number' => $transactionNumber,
                'total'              => $total,
                'payment_method'     => $paymentMethod,
                'cashier_name'       => $cashierName,
                'customer_name'      => $customerName,
                'timestamp'          => date('Y-m-d H:i:s'),
            ];

            // Broadcast ke 2 channel:
            // 1. Global channel untuk admin (semua outlet)
            $this->pusher->trigger('transactions-global', 'transaction-created', $data);

            // 2. Outlet-specific channel untuk manager
            $this->pusher->trigger("transactions-{$outletId}", 'transaction-created', $data);

            log_message('info', "New transaction broadcasted: {$transactionNumber} at Outlet #{$outletId} - Total: Rp " . number_format($total, 0, ',', '.'));

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to broadcast new transaction: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast custom event
     * 
     * @param string $channel Channel name
     * @param string $event   Event name
     * @param array  $data    Data to broadcast
     * @return bool
     */
    public function broadcast(string $channel, string $event, array $data): bool
    {
        if ($this->pusher === null) {
            return false;
        }

        try {
            $this->pusher->trigger($channel, $event, $data);
            return true;
        } catch (\Exception $e) {
            log_message('error', "Failed to broadcast event {$event} on channel {$channel}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Pusher instance
     */
    public function getPusher(): ?Pusher
    {
        return $this->pusher;
    }
}
