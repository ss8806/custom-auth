<?php

declare(strict_types=1);

namespace App\Auth\Passwords;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use App\Auth\Passwords\CustomTokenRepository;

use function base64_decode;
use function substr;

class PasswordManager extends PasswordBrokerManager
{
    // 省略
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $connection = $config['connection'] ?? null;
        // ① ★独自のDatabaseTokenRepositoryを指定します。
        return new CustomTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }

    // public function sendResetLink(array $credentials, Closure $callback = null)
    // {
    //     // TODO: Implement sendResetLink() method.
    // }

    // public function reset(array $credentials, Closure $callback)
    // {
    //     // TODO: Implement reset() method.
    // }

    // public function validator(Closure $callback)
    // {
    //     // TODO: Implement validator() method.
    // }

    // public function validateNewPassword(array $credentials)
    // {
    //     // TODO: Implement validateNewPassword() method.
    // }
}
