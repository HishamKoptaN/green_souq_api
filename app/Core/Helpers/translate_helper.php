<?php

use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;

function autoTrans($text, $targetLocale)
{
    return Cache::remember("trans_{$targetLocale}_".md5($text), now()->addMonth(1), function () use ($text, $targetLocale) {
        $tr = new GoogleTranslate;
        $tr->setSource('ar');
        $tr->setTarget($targetLocale);

        return $tr->translate($text);
    });
}
