<?php

declare(strict_types=1);

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

class CustomTokenRepository extends DatabaseTokenRepository
{
    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $email = $user->getEmailForPasswordReset();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        // ★独自の管理用の列の値を取得（※実際はもっと複雑な取得をしていますが例のため簡単にしています）
        $dummy = 'dummy.value';
        // ★独自の管理用の列dummyを更新するためにgetPayloadではなく独自のメソッドgetMyPayloadを呼び出す
        $this->getTable()->insert($this->getMyPayload($email, $token, $dummy));

        return $token;
    }

    /**
     * Build the record payload for the table.
     * ★管理用の列更新のためgetPayloadをコピーして作成
     * 引数に$dummyが追加され、配列に$dummyをセットするだけ
     *
     * @param  string  $email
     * @param  string  $token
     * @param  string  $dummy
     * @return array
     */
    protected function getMyPayload($email, $token, $dummy)
    {
        // ★dummyを追加
        return ['email' => $email, 'token' => $this->hasher->make($token), 'created_at' => new Carbon, 'dummy' => $dummy];
    }
}
