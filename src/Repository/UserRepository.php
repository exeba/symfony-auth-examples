<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Seb\AuthenticatorBundle\Security\CredentialsProviders\FormCredentials;
use Seb\AuthenticatorBundle\Security\UserManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @method User|null findOneByUsername(string $username)
 * @method User|null findOneByEmail(string $email)
 *
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserManagerInterface
{
    private $passwordEncoder;

    public function __construct(
        ManagerRegistry $registry,
        UserPasswordHasherInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser($userData)
    {
        if ($userData instanceof FormCredentials) {
            $user = new User();
            $user->setUsername($userData->getUsername());
            $user->setFullName($userData->getUsername());
            $user->setEmail($userData->getUsername().'@mail.com');
            $user->setPassword($this->passwordEncoder->hashPassword($user, $userData->getPassword()));
            $user->setRoles(['ROLE_USER']);

            return $user;
        }
    }

    public function persistUser(UserInterface $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
