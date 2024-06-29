<?php

namespace ReviewX\Controllers\Storefront\Modules;

class ReCaptcha
{
    public function getAllow()
    {
        return get_option('_rx_option_allow_recaptcha', 0);
    }

    public function getSiteKey()
    {
        return get_option('_rx_option_recaptcha_site_key', null);
    }

    public function getSecretKey()
    {
        return get_option('_rx_option_recaptcha_secret_key', null);
    }

    public static function isEnable()
    {
        $self = new self;
        return $self->getAllow() && $self->getSiteKey() && $self->getSecretKey();
    }

    public static function showField()
    {
        $isEnable = self::isEnable();
        $siteKey = (new self())->getSiteKey();

        if ($isEnable) {
            return "<script src='https://www.google.com/recaptcha/api.js?render=" . $siteKey . "'></script>
            <script>

                let siteKey = '$siteKey';
                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, {action:'validate_captcha'})
                            .then(function(token) {
                                console.log(token);
                    });
                });
            </script>";
        } else {
            return "<p class='comment-form-recaptcha'><input type=\"hidden\" id=\"enable_recaptcha\" value=\"0\"></p>";
        }
    }
}