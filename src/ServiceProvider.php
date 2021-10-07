<?php

namespace Czhou\Yidun;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(CaptchaVerifier::class, function(){
            $secretPair = new SecretPair(config('services.YiDunCaptchaVerifier.secret_id'), config('services.YiDunCaptchaVerifier.secret_key'));
            return new CaptchaVerifier(config('services.YiDunCaptchaVerifier.captcha_id'), $secretPair);
        });

        $this->app->alias(CaptchaVerifier::class, 'YiDunCaptchaVerifier');
    }

    public function provides()
    {
        return [CaptchaVerifier::class, 'YiDunCaptchaVerifier'];
    }
}