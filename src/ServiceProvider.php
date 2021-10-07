<?php

namespace Czhou\Yidun;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(CaptchaVerifier::class, function(){
            $secretPair = new SecretPair(config('services.YiDun.secret_id'), config('services.YiDun.secret_id'));
            return new CaptchaVerifier(config('services.YiDun.captcha_id'), $secretPair);
        });

        $this->app->alias(CaptchaVerifier::class, 'YiDunCaptchaVerifier');
    }

    public function provides()
    {
        return [CaptchaVerifier::class, 'YiDunCaptchaVerifier'];
    }
}