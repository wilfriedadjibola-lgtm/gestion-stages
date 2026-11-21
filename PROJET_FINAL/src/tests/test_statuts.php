<?php
// Tests unitaires des règles métier (sans base de données). Usage : php src/tests/test_statuts.php
declare(strict_types=1);
require __DIR__ . '/../Statut.php';

$ok = 0; $ko = 0;
$test = function (string $nom, bool $cond) use (&$ok, &$ko) {
    $cond ? $ok++ : $ko++;
    echo ($cond ? 'OK    ' : 'ECHEC ') . $nom . "\n";
};

$test('TC-01 BROUILLON -> SOUMISE autorisée', Statut::verifier('BROUILLON', 'SOUMISE', null) === null);
$test('TC-02 SOUMISE -> VALIDEE autorisée', Statut::verifier('SOUMISE', 'VALIDEE', null) === null);
$test('TC-03 SOUMISE -> REFUSEE avec motif autorisée', Statut::verifier('SOUMISE', 'REFUSEE', 'Dossier incomplet') === null);
$test('TC-04 SOUMISE -> REFUSEE sans motif refusée', Statut::verifier('SOUMISE', 'REFUSEE', '') !== null);
$test('TC-05 REFUSEE -> BROUILLON autorisée', Statut::verifier('REFUSEE', 'BROUILLON', null) === null);
$test('TC-06 BROUILLON -> VALIDEE interdite', Statut::verifier('BROUILLON', 'VALIDEE', null) !== null);
$test('TC-07 VALIDEE -> BROUILLON interdite', Statut::verifier('VALIDEE', 'BROUILLON', null) !== null);
$test('TC-08 VALIDEE est un état final', Statut::TRANSITIONS['VALIDEE'] === []);
$test('TC-09 validation réservée à ADMIN / RESPONSABLE', in_array('ADMIN', Statut::rolesPour('VALIDEE'), true) && in_array('RESPONSABLE', Statut::rolesPour('VALIDEE'), true));
$test('TC-10 soumission réservée à ETUDIANT', Statut::rolesPour('SOUMISE') === ['ETUDIANT']);

echo "\n$ok réussis, $ko échoués\n";
exit($ko ? 1 : 0);
