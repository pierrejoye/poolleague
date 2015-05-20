<?php

namespace Pool\Entity;

use Predis\Client;

/**
 * Class LeagueRepository.
 */
class LeagueRepository
{
    const LEAGUE_HASH_STORE = 'leagues';

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
     * @param Team
     */
    public function persist(League $league)
    {
        $id = $league->getId();
        if (!$id) {
            $id = $this->redisClient->incr(self::LEAGUE_HASH_STORE.'_id');
            $league->setId($id);
        }

        $this->redisClient->hset(self::LEAGUE_HASH_STORE, $id, serialize($league));
    }

    /**
     * @param Team
     */
    public function remove(League $league)
    {
        $id = $league->getId();
        $tx->hdel(self::LEAGUE_HASH_STORE, $id);
    }

    /**
     * @param string $email
     *
     * @return Team|null
     */
    public function find($leagueId)
    {
        $league = $this->redisClient->hget(self::LEAGUE_HASH_STORE, strtolower(trim($leagueId)));

        return empty($league) ? null : unserialize($league);
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $leagues = $this->redisClient->hgetall(self::LEAGUE_HASH_STORE);

        if ($leagues && is_array($leagues)) {
            $result = [];
            foreach ($leagues as $league) {
                $result[] = unserialize($league);
            }

            return $result;
        }

        return;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
