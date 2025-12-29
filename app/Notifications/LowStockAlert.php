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
            ->subject('Low Stock Alert: ' . $this->inventory->item_name)
            ->line('The stock for item "' . $this->inventory->item_name . '" has dropped below the threshold.')
            ->line('Current Stock: ' . $this->inventory->stock_qty . ' ' . $this->inventory->unit)
            ->line('Threshold: ' . $this->inventory->alert_threshold)
            ->action('Restock Now', route('inventory.restock.form', $this->inventory))
            ->line('Please restock as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Low Stock Alert',
            'message' => "Item {$this->inventory->item_name} is low on stock ({$this->inventory->stock_qty} {$this->inventory->unit}).",
            'inventory_id' => $this->inventory->id,
            'link' => route('inventory.restock.form', $this->inventory),
        ];
    }
}
