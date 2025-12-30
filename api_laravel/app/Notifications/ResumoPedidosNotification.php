<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ResumoPedidosNotification extends Notification
{
    use Queueable;

    public function __construct(protected Collection $resumo) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        $email = (new MailMessage)
            ->subject('Resumo de Pedidos - Últimos 7 Dias')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Aqui está o resumo dos pedidos processados nos últimos 7 dias, separados por status:');

        foreach ($this->resumo as $item) {
            $email->line("- **{$item->status}**: {$item->total} pedido(s) (Total: R$ " . number_format($item->soma, 2, ',', '.') . ")");
        }

        return $email->line('Obrigado por utilizar o sistema Constance!');
    }
}
