<?php

namespace Nebula\Framework\Auth;

use App\Models\User;

class Auth
{
    public static function user(): ?User
    {
        $id = session()->get("user_id");
        $uuid = $_COOKIE["user_uuid"] ?? null;
        if ($id) {
            return new User($id);
        } elseif ($uuid) {
            $user = User::findByAttribute("uuid", $uuid);
            return new User($user->id);
        }
        return null;
    }

    public static function redirectSignIn(): void
    {
        header("Location: /sign-in");
        exit();
    }

    public static function redirectProfile(): void
    {
        $route = config("security.sign_in_route");
        header("Location: $route");
        exit();
    }

    public static function successfulSignIn(): void
    {
        $route = config("security.sign_in_route");
        header("HX-Location: $route");
        exit();
    }

    public static function signIn(User $user, bool $remember_me = false): void
    {
        session()->set("user_id", $user->id);
        if ($remember_me) {
            $future_time = time() + 86400 * 30;
            setcookie("user_uuid", $user->uuid, $future_time, "/");
        }
        $user->login_at = date("Y-m-d H:i:s");
        $user->save();
        self::successfulSignIn();
    }

    public static function signOut(): void
    {
        session()->destroy();
        unset($_COOKIE["user_uuid"]);
        setcookie("user_uuid", "", -1, "/");
        self::redirectSignIn();
    }

    public static function hashPassword(string $password): string|bool|null
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    public static function userAuth(array $data): User|false
    {
        $user = User::findByAttribute("email", $data["email"]);
        if ($user) {
            $password_valid = password_verify(
                $data["password"],
                $user->password
            );
            if ($password_valid) {
                return $user;
            }
        }
        return false;
    }

    public static function registerUser(array $data): User
    {
        $data["password"] = self::hashPassword($data["password"]);
        $user = User::new($data);
        return $user;
    }
}
