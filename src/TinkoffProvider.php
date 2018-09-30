<?php namespace professionalweb\payment;

use Illuminate\Support\ServiceProvider;
use professionalweb\payment\contracts\PayService;
use professionalweb\payment\contracts\PaymentFacade;
use professionalweb\payment\interfaces\TinkoffService;
use professionalweb\payment\drivers\tinkoff\TinkoffDriver;
use professionalweb\payment\drivers\tinkoff\TinkoffProtocol;

/**
 * Tinkoff payment provider
 * @package professionalweb\payment
 */
class TinkoffProvider extends ServiceProvider
{

    public function boot(): void
    {
        app(PaymentFacade::class)->registerDriver(TinkoffService::PAYMENT_TINKOFF, TinkoffService::class);
    }


    /**
     * Bind two classes
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TinkoffService::class, function ($app) {
            return (new TinkoffDriver(config('payment.tinkoff')))->setTransport(
                new TinkoffProtocol(config('payment.tinkoff.merchantId'), config('payment.tinkoff.secretKey'), config('payment.tinkoff.apiUrl'))
            );
        });
        $this->app->bind(PayService::class, function ($app) {
            return (new TinkoffDriver(config('payment.tinkoff')))->setTransport(
                new TinkoffProtocol(config('payment.tinkoff.merchantId'), config('payment.tinkoff.secretKey'), config('payment.tinkoff.apiUrl'))
            );
        });
        $this->app->bind(TinkoffDriver::class, function ($app) {
            return (new TinkoffDriver(config('payment.tinkoff')))->setTransport(
                new TinkoffProtocol(config('payment.tinkoff.merchantId'), config('payment.tinkoff.secretKey'), config('payment.tinkoff.apiUrl'))
            );
        });
        $this->app->bind('\professionalweb\payment\Tinkoff', function ($app) {
            return (new TinkoffDriver(config('payment.tinkoff')))->setTransport(
                new TinkoffProtocol(config('payment.tinkoff.merchantId'), config('payment.tinkoff.secretKey'), config('payment.tinkoff.apiUrl'))
            );
        });
    }
}