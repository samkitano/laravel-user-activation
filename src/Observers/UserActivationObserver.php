<?php

namespace Kitano\UserActivation\Observers;

use Kitano\UserActivation\Services\UserActivationService;

class UserActivationObserver
{
    /** @var UserActivationService */
    protected $activationService;

    /**
     * @param                       $user
     * @param UserActivationService $activationService
     */
    public function __construct($user, UserActivationService $activationService)
    {
        $this->activationService = $activationService;
    }

    /**
     * @param $user
     */
    public function created($user)
    {
        $this->activationService->createActivation($user);
    }
}
