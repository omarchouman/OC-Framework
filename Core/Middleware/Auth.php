<?php

namespace Core\Middleware;

class Auth
{
    public function handle(): void
    {
        if (!isset($_SESSION['user']) || !$_SESSION['user']) {
            abort(403);
        }
    }
}