<?php

namespace App\Services;

class FlashMessageService
{
    public function success(string $message): void
    {
        session()->flash('message', $message);
    }

    public function error(string $message): void
    {
        session()->flash('error', $message);
    }

    public function info(string $message): void
    {
        session()->flash('info', $message);
    }

    public function warning(string $message): void
    {
        session()->flash('warning', $message);
    }
}
