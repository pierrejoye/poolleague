<?php

namespace Pool\Entity;

use Predis\Client;

/**
 * Class TournamentRepository.
 */
class TournamentRepository
{
    const TOURNAMENT_HASH_STORE = 'tournaments';
    const LEAGUE2TOURNAMENT = 'league2'.self::TOURNAMENT_HASH_STORE:
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
    public function persist(Tournament $tournament)
    {
        $id = $tournament->getId();
        if (!$id) {
            $id = $this->redisClient->incr(self::TOURNAMENT_HASH_STORE.'_id');
            $tournament->setId($id);
        }

        $this->redisClient->hset(self::TOURNAMENT_HASH_STORE, $id, serialize($tournament));
    }

    /**
     * @param Team
     */
    public function remove(Tournament $tournament)
    {
        $id = $tournament->getId();
        $tx->hdel(self::TOURNAMENT_HASH_STORE, $id);
    }

    /**
     * @param string $email
     *
     * @return Team|null
     */
    public function find($tournamentId)
    {
        $tournament = $this->redisClient->hget(self::TOURNAMENT_HASH_STORE, strtolower(trim($tournamentId)));

        return empty($tournament) ? null : unserialize($tournament);
    }


    /**
     * @return array|null
     */
    public function getByLeague(League $league)
    {
        $tournaments = $this->redisClient->hgetall(LEAGUE2TOURNAMENT);
        
        if ($tournaments && is_array($tournaments)) {
            $result = [];
            foreach ($tournaments as $tournament) {
                $result[] = unserialize($tournament);
            }

            return $result;
        }

        return;
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $tournaments = $this->redisClient->hgetall(self::TOURNAMENT_HASH_STORE);

        if ($tournaments && is_array($tournaments)) {
            $result = [];
            foreach ($tournaments as $tournament) {
                $result[] = unserialize($tournament);
            }

            return $result;
        }

        return;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
