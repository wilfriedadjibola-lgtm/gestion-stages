
<?php

final class AuthService
{
    public function __construct(
        private UtilisateurRepository $users
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (
            !$user ||
            !password_verify($password, $user['mot_de_passe'])
        ) {
            throw new RuntimeException(
                'Identifiants incorrects.',
                401
            );
        }

        unset($user['mot_de_passe']);

        return $user;
    }
}
