<?php

namespace common\components;

use yii\web\View;

class CustomView extends View
{
    public $cspNonce;

    protected function addNonceToTags($html)
    {
        // Add nonce to <script> tags
        $html = preg_replace_callback(
            '/<script(?![^>]+nonce=)([^>]*)>/i',
            fn($matches) => "<script{$matches[1]} nonce=\"{$this->cspNonce}\">",
            $html
        );

        // Add nonce to <style> tags
        $html = preg_replace_callback(
            '/<style(?![^>]+nonce=)([^>]*)>/i',
            fn($matches) => "<style{$matches[1]} nonce=\"{$this->cspNonce}\">",
            $html
        );

        return $html;
    }

    public function renderHeadHtml()
    {
        return $this->addNonceToTags(parent::renderHeadHtml());
    }

    public function renderBodyBeginHtml()
    {
        return $this->addNonceToTags(parent::renderBodyBeginHtml());
    }

    public function renderBodyEndHtml($ajaxMode)
    {
        return $this->addNonceToTags(parent::renderBodyEndHtml($ajaxMode));
    }

    public function renderDynamic($statements)
    {
        return $this->addNonceToTags(parent::renderDynamic($statements));
    }
}
