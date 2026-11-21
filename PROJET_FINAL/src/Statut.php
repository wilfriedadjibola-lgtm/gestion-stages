<?php
declare(strict_types=1);

/** Cycle de vie d'une demande de stage (règles métier). */
final class Statut
{
    public const TRANSITIONS = [
        'BROUILLON' => ['SOUMISE'],
        'SOUMISE'   => ['VALIDEE', 'REFUSEE'],
        'REFUSEE'   => ['BROUILLON'],
        'VALIDEE'   => [],
    ];

    public static function permise(string $ancien, string $nouveau): bool
    {
        return in_array($nouveau, self::TRANSITIONS[$ancien] ?? [], true);
    }

    /** Renvoie un message d'erreur, ou null si la transition est valide. */
    public static function verifier(string $ancien, string $nouveau, ?string $motif): ?string
    {
        if (!self::permise($ancien, $nouveau)) {
            return "Transition interdite : $ancien vers $nouveau";
        }
        if ($nouveau === 'REFUSEE' && strlen(trim((string) $motif)) < 5) {
            return 'Un motif de refus (5 caractères minimum) est obligatoire';
        }
        if ($nouveau === 'REFUSEE' && strlen(trim((string) $motif)) > 255) {
            return 'Le motif de refus ne doit pas dépasser 255 caractères';
        }
        return null;
    }

    /** Rôles autorisés à effectuer la transition. */
    public static function rolesPour(string $nouveau): array
    {
        return in_array($nouveau, ['VALIDEE', 'REFUSEE'], true) ? ['ADMIN', 'RESPONSABLE'] : ['ETUDIANT'];
    }
}
