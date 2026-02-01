<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
        return (new MailMessage())
            ->subject('Bem-vindo ao VetClinic+! Confirme seu acesso')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seja bem-vindo ao VetClinic+. Estamos prontos para ajudar na gestão da sua clínica.')
            ->line('Para garantir a segurança dos seus dados, precisamos confirmar que este e-mail é realmente seu.')
            ->action('Confirmar meu E-mail', $url)
            ->line('Se você não criou esta conta, nenhuma ação é necessária.')
            ->salutation('Atenciosamente, Equipe VetClinic+');
    });
    }
}
