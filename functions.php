<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 24/2/17
 * Time: 8:09 PM
 */

require_once "config.php";

function getAuctionStateFile($round, $checkIfExists = false)
{
    $fileName = "auction_states/round" . $round . ".txt";
    if(!$checkIfExists || ($checkIfExists && file_exists($fileName)))
    {
        return $fileName;
    }
    return null;
}

function getInitialState()
{
    $players = getPlayers();
    $leagueTeams = getAuctionTeams();

    $auctionState = new AuctionState();

    $auctionState->round = 1;
    $auctionState->leagueTeams = $leagueTeams;
    $auctionState->players = $players;

    foreach ($players as $player)
    {
        $auctionState->allRemainingPlayers[$player->id] = $player;
        $auctionState->allRemainingPlayersCount++;
    }

    $json = json_encode($auctionState);
    return $json;
}

function getPlayers()
{
    $input = file_get_contents("data/players.txt");
    $lines = explode("\n", $input);

    $players= array();
    foreach ($lines as $line)
    {
        $playerData = explode(",", $line);
        $player = new Player();

        $id = $playerData[0];
        $player->id = $id;
        //$player->nationality = $playerData[0];
        $player->name = $playerData[1];
        $player->role = $playerData[2];
        $player->iplTeam = $playerData[3];
        $player->isStar = ($playerData[3] >=3) ? true : false;

        $players[$id] = $player;
    }

    return $players;
}

function getAuctionTeams()
{
    $input = file_get_contents("data/leagueTeams.txt");
    $lines = explode("\n", $input);

    $leagueTeams= array();
    $id = 1;
    foreach ($lines as $line)
    {
        $leagueTeamData = explode(",", $line);
        $leagueTeam = new AuctionTeam();

        $leagueTeam->id = $id;
        $leagueTeam->name = $leagueTeamData[0];
        $leagueTeam->budgetLeft = $leagueTeamData[1];
        $leagueTeam->actions = array();

        $leagueTeams[$id] = $leagueTeam;

        $id++;
    }

    return $leagueTeams;
}

function safeReturn($array, $index, $default=null)
{
    return isset($array[$index]) ? $array[$index] : $default;
}

function isStringSet($string)
{
    return (!is_null($string) && $string !== '');
}

function getTotalRounds()
{
    $round = file_get_contents("auction_states/totalRounds.txt");
    return isStringSet($round) ? $round : 0;
}

function setTotalRounds($round)
{
    file_put_contents("auction_states/totalRounds.txt", $round);
}

function resetAuctionToRound($round)
{
    $totalRounds = getTotalRounds();
    for($i=$round+1;$i<=$totalRounds;$i++)
    {
        $fileName = getAuctionStateFile($i, true);
        if(isStringSet($fileName))
        {
            unlink($fileName);
        }
    }
    setTotalRounds($round);
}

function isUserAdmin()
{
    return isStringSet(safeReturn($_COOKIE, "auction_admin"));
}

function getRules($tournamentName)
{
    $rulesInputFile = TOURNAMENTS_HOME_DIR . "/" . $tournamentName . "/rules.txt";
    $rulesInput = explode("\n", file_get_contents($rulesInputFile));

    //TODO: This should come from config?
    $types = array("value", "range", "boolean", "perQty");
    $parameters = array
    (
        "notout" => new Parameter("notOut", "Not Out"),
        "runs" => new Parameter("runs", "Runs"),
        "boundaries" => new Parameter("boundaries", "4s and 6s"),
        "strikerate" => new Parameter("strikerate", "Strike Rate"),
        "wickets" => new Parameter("wickets", "Wickets"),
        "dots" => new Parameter("dots", "Dot Balls"),
        "maidens" => new Parameter("maidens", "Maidens"),
        "economy" => new Parameter("economy", "Economy"),
        "hattrick" => new Parameter("hattrick", "Hat Trick"),
        "fielding" => new Parameter("fielding", "Catches / Stumpings / Run Outs"),
        "MoM" => new Parameter("MoM", "Man of the Match"),
        "MoS" => new Parameter("MoM", "Man of the Series"),
        "winningTeam" => new Parameter("winningTeam", "Part of Winning Team"),
        "losingTeam" => new Parameter("losingTeam", "Part of Losing Team"),
    );
    $rules = array();

    foreach ($rulesInput as $index=>$line)
    {
        if($index == 0) continue; //Its header row

        $curRule = explode(",", $line);

        $rule = new Rule();
        if(isStringSet($curRule[0]))
        {
            $rule->id = $curRule[0];
        }
        else
        {
            echo "Line " . $index . " - Id not provided<br>";
        }
        if(isStringSet($curRule[1]))
        {
            if(array_key_exists($curRule[1], $parameters))
            {
                $rule->parameter = $curRule[1];
            }
            else
            {
                echo "Line " . $index . " - Invalid parameter: " . $curRule[1] . "<br>";
            }
        }
        else
        {
            echo "Line " . $index . " - Parameter not provided<br>";
        }
        if(isStringSet($curRule[2]))
        {
            if(in_array($curRule[2], $types))
            {
                $rule->type = $curRule[2];
            }
            else
            {
                echo "Line " . $index . " - Invalid type: " . $curRule[2] . "<br>";
            }
        }
        else
        {
            echo "Line " . $index . " - Type not provided<br>";
        }

        switch ($curRule[2])
        {
            case "value":
                if(isStringSet($curRule[3]))
                {
                    if(is_numeric($curRule[3]))
                    {
                        $rule->value = (double)$curRule[3];
                    }
                    else
                    {
                        echo "Line " . $index . " - Invalid value: " . $curRule[3] . "<br>";
                    }
                }
                else
                {
                    echo "Line " . $line .  " - Value not specified<br>";
                }
                break;

            case "range":
                if(isStringSet($curRule[4]) || isStringSet($curRule[5]))
                {
                    if(!is_numeric($curRule[4]))
                    {
                        echo "Line " . $index . " - Invalid lower bound: " . $curRule[4] . "<br>";
                        break;
                    }
                    else if(!is_numeric($curRule[4]))
                    {
                        echo "Line " . $index . " - Invalid lower bound: " . $curRule[4] . "<br>";
                    }
                    else
                    {
                        $rule->lowerBound = isStringSet($curRule[4]) ? (double)$curRule[4] : null;
                        $rule->upperBound = isStringSet($curRule[5]) ? (double)$curRule[5] : null;
                    }
                }
                else
                {
                    echo "Line " . $line .  " - Range not specified<br>";
                }
                break;
        }
        if(isStringSet($curRule[6]))
        {
            $rule->points = (double)$curRule[6];
        }
        if(isStringSet($curRule[7]))
        {
            $rule->overrideRuleId = $curRule[7];
        }
        $rules[$rule->id] = $rule;
        $parameters[$rule->parameter]->ruleIds[] = $rule->id;
    }

    $ruleData = array
    (
        "rules" => $rules,
        "parameters" => $parameters
    );

    return $ruleData;
}

function getLeagueTeams($tournament, $league)
{
    $teamsInputFile = TOURNAMENTS_HOME_DIR . "/" . $tournament . "/" . $league . "/teams.txt";
    $teamsInput = explode("\n", file_get_contents($teamsInputFile));

    $leagueTeams = array();
    
    foreach ($teamsInput as $index => $line)
    {
        $curTeam = explode(",", $line);
        $team = new LeagueTeam();
        $team->id = $index;

        $team->ownerName = $curTeam[0];
        $team->teamName = $curTeam[1];

        $length = count($curTeam);
        for($i=2; $i<$length; $i++)
        {
            $tournamentPlayer = getTournamentPlayer($tournament, $curTeam[$i]);
            if($tournamentPlayer==null)
            {
                echo "sdfsdfsf<br>";
                echo $curTeam[$i] . "<br>";
            }
            $leaguePlayer = new LeaguePlayer();
            $leaguePlayer->id = $tournamentPlayer->id;
            $team->leaguePlayers[] = $leaguePlayer;
        }

        $leagueTeams[$team->id] = $team;
    }

    return $leagueTeams;
}

function getTournamentPlayer($tournament, $playerName)
{
    $players = getTournamentPlayers($tournament);
    foreach ($players as $id=>$player)
    {
        if($player->name == $playerName)
        {
            return $player;
        }
    }

    return null;
}

function getRealPlayerName($playerName)
{
    $teamStart = strpos($playerName, "(");
    if($teamStart===FALSE)
    {
        return $playerName;
    }
    return substr($playerName, 0, $teamStart-1);
}

function getTournamentPlayers($tournament)
{
    $playersInputFile = TOURNAMENTS_HOME_DIR . "/" . $tournament . "/players.txt";
    $playersInput = explode("\n", file_get_contents($playersInputFile));

    $players = array();

    foreach ($playersInput as $index=>$line)
    {
        if($index==0) continue; //Header row
        $curPlayer = explode(",", $line);

        $player = new TournamentPlayer();
        $player->id = (string)$curPlayer[4];
        $player->role = $curPlayer[2];
        $player->name = $curPlayer[1];
        $player->cricketTeam = $curPlayer[0];

        $players[$player->id] = $player;
    }

    return $players;
}

function getTournamentLeagues($tournament)
{
    $leagues = array();
    $leaguesInputFile = TOURNAMENTS_HOME_DIR . "/" . $tournament . "/leagues.txt";
    $leaguesInput = explode("\n", file_get_contents($leaguesInputFile));

    foreach ($leaguesInput as $index=>$line)
    {
        $curLeague = explode(",", $line);
        $leagues[] = new League($curLeague[0], $curLeague[1]);
    }

    return $leagues;
}

function getTeamId($timestamp)
{
    $timestamp = explode(" ", $timestamp);
    $time = $timestamp[1];
    $timeArr = explode(":", $time);
    $teamId = implode("", $timeArr);
    $teamId = strval($teamId);
    if(strlen($teamId)==5)
    {
        $teamId = "0" . $teamId;
    }
    return strval($teamId);
}

function getTeamJson($teamLine, $teamHeader, $onlyDisplay = false)
{
    $teamVals = explode(",", $teamLine);
    $headerVals = explode(",", $teamHeader);
    $team = array("headers"=>array());
    foreach ($teamVals as $index=>$teamVal)
    {
        $headerVal = $headerVals[$index];
        if($onlyDisplay)
        {
            if($headerVal!="Your name" && $headerVal!="Team Name")
            {
                continue;
            }
        }
        $team["headers"][] = $headerVal;
        $team[$headerVal] = $teamVal;
    }
    return $team;
}

function getMatchNo($tournament)
{
    $matchNo = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/matchCount.txt");
    if(isStringSet($matchNo))
    {
        return intval($matchNo);
    }
    return 0;
}