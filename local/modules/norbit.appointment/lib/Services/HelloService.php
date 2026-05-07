<?php

namespace Norbit\Appointment\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Web\Json;

class HelloService
{
    public function hello(): array
    {
        return [
            'hello' => 'world3',
        ];
    }
}
