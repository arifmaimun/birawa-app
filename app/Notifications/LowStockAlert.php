<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $inventory;

    /**
     * Create a new notification instance.
     */
    public function __construct($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.low_stock.subject', ['item_name' => $this->inventory->item_name]))
            ->line(__('notifications.low_stock.line1', ['item_name' => $this->inventory->item_name]))
            ->line(__('notifications.low_stock.line2', ['stock_qty' => $this->inventory->stock_qty, 'unit' => $this->inventory->unit]))
            ->line(__('notifications.low_stock.line3', ['threshold' => $this->inventory->alert_threshold]))
            ->action(__('notifications.low_stock.action'), route('inventory.restock', $this->inventory))
            ->line(__('notifications.low_stock.line4'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('notifications.low_stock.title'),
            'message' => __('notifications.low_stock.message', ['item_name' => $this->inventory->item_name, 'stock_qty' => $this->inventory->stock_qty, 'unit' => $this->inventory->unit]),
            'inventory_id' => $this->inventory->id,
            'link' => route('inventory.restock', $this->inventory),
        ];
    }
}
