<?php
declare(strict_types=1);

final class Env
{
    public static function load(string $file): void
    {
        if (!is_file($file)) {
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
            $ligne = trim($ligne);
            if ($ligne === '' || $ligne[0] === '#' || !str_contains($ligne, '=')) {
                continue;
            }
            [$cle, $valeur] = explode('=', $ligne, 2);
            $_ENV[trim($cle)] = trim($valeur, " \t\"'");
        }
    }

    public static function get(string $cle, string $defaut = ''): string
    {
        return $_ENV[$cle] ?? (getenv($cle) ?: $defaut);
    }
}
