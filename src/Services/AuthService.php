<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public static function login(int $userId)
    {
        $_SESSION['user_id'] = $userId;
    }

    public static function logout()
    {
        session_destroy();
        unset($_SESSION['user_id']);
    }

    public static function user()
    {
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->where('id', '=', $_SESSION['user_id'])->first();
            if ($user) {
                return $user;
            }
            session_destroy();
            unset($_SESSION['user_id']);
        }

        return null;
    }
}

?>