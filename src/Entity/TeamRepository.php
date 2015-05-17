<?php

namespace Pool\Entity;

use Predis\Client;
use Predis\Transaction\MultiExec;

/**
 * Class UserRepository.
 */
class TeamRepository
{
    const TEAM_HASH_STORE = 'teams';

    /**
     * @var Client
     */
    protected $redisClient;

    /**
     * @param Client $redisClient
     */
    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param User $user
     */
    public function persist(Team $team)
    {
        $this->redisClient->transaction(
            function (MultiExec $tx) use ($team) {
                $id = $user->getId();
                $tx->hset(self::TEAM_HASH_STORE, $id, serialize($team));
            }
        );
    }

    /**
     * @param User $user
     */
    public function remove(Team $team)
    {
        $this->redisClient->transaction(
            function (MultiExec $tx) use ($team) {
                $id = $user->getId();
                $tx->hdel(self::TEAM_HASH_STORE, $id);
        );
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function find($teamId)
    {
        $user = $this->redisClient->hget(self::TEAM_HASH_STORE, strtolower(trim($teamId)));

        return empty($team) ? null : unserialize($team);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
