<?php

namespace App\Components;

class Session
{
    /**
     * Inicia uma sessão caso não exista
     */
    public static function start(): void
    {
        if (! session_id()) {
            session_save_path(SESSION);
            session_start();
        }
    }

    public static function all(): ?array
    {
        self::start();
        return $_SESSION ?? null;
    }

    public static function one(string $key = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = (is_array($value) ? $value : $value);
    }

    public static function unset(string $key = null): void
    {
        self::start();
        if ($key) {
            unset($_SESSION[$key]);
        } else {
            session_unset();
        }
    }

    public static function has(string $key = null): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function regeneration_id(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
    }
}
