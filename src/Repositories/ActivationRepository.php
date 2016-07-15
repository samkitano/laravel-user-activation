<?php

namespace Kitano\UserActivation\Repositories;

use Kitano\UserActivation\Models\Activation;

class ActivationRepository extends Activation
{
    /**
     * Get an activation by its token
     *
     * @param $token
     *
     * @return mixed
     */
    public function findByToken($token)
    {
        return $this->find($token);
    }

    /**
     * Delete an activation.
     *
     * @param $token
     */
    public function destroyActivation($token)
    {
        $this->find($token)->delete();
    }

    /**
     * Retrieve an activation by user id
     *
     * @param $id
     *
     * @return mixed
     */
    public function findById($id)
    {
        return $this->where('user_id', $id)->first();
    }

    /**
     * Count how many tokens do we have yet for activation
     *
     * @return int
     */
    public function count()
    {
        return $this->all()->count();
    }

    /**
     * Retrieve all activations
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->all();
    }
}
