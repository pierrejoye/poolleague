<?php

namespace Pool\Util\Fixture;

class TwoWays
{
    protected $data;
    protected $rounds;
    protected $countTeams;

    public function __construct()
    {
        $this->data = $data;
        $this->rounds = $rounds;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getRounds()
    {
        return $this->rounds;
    }

    public function getRoundsCount()
    {
        return $this->roundsCouunt;
    }

    public function generate()
    {
        $countTeams = count($this->data);
        $teamsId = $this->data;

        $ghost = false;
        if ($countTeams % 2 == 1) {
            $countTeams++;
            $ghost = true;
        }

        $totalRounds = $countTeams - 1;
        $matchesPerRound = $countTeams / 2;
        $rounds = [];
        for ($i = 0; $i < $totalRounds; $i++) {
            $rounds[$i] = [];
        }

        for ($round = 0; $round < $totalRounds; $round++) {
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($round + $match) % ($countTeams - 1);
                $away = ($countTeams - 1 - $match + $round) % ($countTeams - 1);

                /* Last team stays in the same place while the others rotate around it.*/
                if ($match == 0) {
                    $away = $countTeams - 1;
                }
                $rounds[$round][$match] = [
                        'home' => $home + 1,
                        'away' => $away + 1,
                    ];
            }
        }

        /* Interleave so that home and away games are fairly evenly dispersed. */
        $interleaved = [];
        for ($i = 0; $i < $totalRounds; $i++) {
            $interleaved[$i] = [];
        }

        $even = 0;
        $odd = ($countTeams / 2);
        for ($i = 0; $i < sizeof($rounds); $i++) {
            if ($i % 2 == 0) {
                $interleaved[$i] = $rounds[$even++];
            } else {
                $interleaved[$i] = $rounds[$odd++];
            }
        }

        $rounds = $interleaved;

        /* Be sure last team is not always 'away'*/
        for ($round = 0; $round < sizeof($rounds); $round++) {
            if ($round % 2 == 1) {
                $t = $this->swap($rounds[$round][0]);
                $rounds[$round][0] = $t;
            }
        }

        $round_counter = sizeof($rounds) + 1;
        $firstRounds = count($rounds) + 1;
        $secondHalfRounds = [];
        $secondHalfCounter = $firstRounds;

        $start = count($rounds) - 1;
        for ($i = $start; $i >= 0; $i--) {
            foreach ($rounds[$i] as $r) {
                $rounds[$round_counter - 1][] = $this->swap($r);
            }
            $round_counter++;
        }

        $this->rounds = $rounds;
        $this->roundsCount = $round_counter;
        $this->ghostTeam = $countTeams;
    }

    protected function swap($match)
    {
        $home = $match['home'];
        $away = $match['away'];
        $match['home'] = $away;
        $match['away'] = $home;

        return $match;
    }
}
