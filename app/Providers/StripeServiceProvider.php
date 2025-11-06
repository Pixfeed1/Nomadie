<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class StripeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('stripe', function ($app) {
            Stripe::setApiKey(config('stripe.secret'));
            return new \Stripe\StripeClient(config('stripe.secret'));
        });
    }

    public function boot()
    {
        //
    }
}