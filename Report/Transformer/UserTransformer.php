<?php

namespace Becklyn\BugsnagBundle\Report\Transformer;

use Bugsnag\Report;
use Becklyn\BugsnagBundle\Report\ReportTransformer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 *
 */
class UserTransformer implements ReportTransformer
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;



    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct (TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * @inheritDoc
     */
    public function transformReport (Report $report, array $monologData)
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token)
        {
            return;
        }

        $user = $token->getUser();

        if (null === $user || !$user instanceof UserInterface)
        {
            return;
        }

        $report->setUser([
            "username" => $user->getUsername(),
            "roles" => $user->getRoles(),
        ]);
    }
}
