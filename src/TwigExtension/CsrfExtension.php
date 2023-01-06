<?php

declare(strict_types=1);

namespace App\TwigExtension;

use Slim\Csrf\Guard;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    protected Guard $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf', [$this, 'csrf'])
        ];
    }

    public function csrf()
    {
        $csrf = '
            <input type="hidden" name="NAME_KEY" value="NAME_VALUE">
            <input type="hidden" name="VALUE_KEY" value="VALUE_VALUE">
        ';

        $csrf = str_replace('NAME_KEY', $this->guard->getTokenNameKey(), $csrf);
        $csrf = str_replace('NAME_VALUE', $this->guard->getTokenName(), $csrf);
        $csrf = str_replace('VALUE_KEY', $this->guard->getTokenValueKey(), $csrf);
        $csrf = str_replace('VALUE_VALUE', $this->guard->getTokenValue(), $csrf);

        return $csrf;
    }
}
