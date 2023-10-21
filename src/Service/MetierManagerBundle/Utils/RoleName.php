<?php

namespace App\Service\MetierManagerBundle\Utils;

/**
 * Class RoleName
 * Classe qui contient les noms constante des rôles utilisateur
 */
class RoleName
{
    // Nom rôle
    const ROLE_SUPER_ADMINISTRATEUR = 'ROLE_SUPERADMIN';
    const ROLE_ADMINISTRATEUR       = 'ROLE_ADMIN';
    const ROLE_ABONNE               = 'ROLE_ABONNE';

    // Identifiant rôle
    const ID_ROLE_SUPERADMIN  = 1;
    const ID_ROLE_ADMIN       = 2;
    const ID_ROLE_ABONNE      = 3;

    static $ROLE_TYPE = array(
        'Admin'      => 'ROLE_ADMIN',
        'Superadmin' => 'ROLE_SUPERADMIN',
        'Abonne'     => 'ROLE_ABONNE'
    );
}
