<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 24/2/17
 * Time: 12:14 AM
 */

$admins = array
(
    "aalhad" => "admin@123",
    "yogesh" => "shikari@123",
    "dnyanesh" => "guruji@123",
    "rajdeep" => "admin@123",
    "amogh" => "misal@123",
    "anupam" => "admin@123"
);

define("DATA_DIRECTORY", "data");
define("TOURNAMENTS_HOME_DIR", DATA_DIRECTORY . "/tournaments");

class AuctionState
{
    public $round; //Integer: Current round number

    public $history; //Object: History of which player was sold to which team at what price from round 1 till now. Null if current round is round 1.

    public $allRemainingPlayers;

    public $players; //Array of Player objects

    public $leagueTeams; //Array of LeagueTeam objects

    public $categories;

    public $unsoldPlayersRemaining;
    public $unsoldPlayers;
    public $soldPlayers;

    public $allRemainingPlayersCount;
    public $soldPlayersCount;
    public $unsoldPlayersCount;
    public $remainingUnsoldPlayersCount;

    public function __construct()
    {
        $this->history = array();
        $this->leagueTeams  =array();
        $this->players = array();
        $this->allRemainingPlayers = array();
        $this->unsoldPlayersRemaining = array();
        $this->unsoldPlayers = array();
        $this->soldPlayers = array();
        $this->allRemainingPlayersCount = 0;
        $this->soldPlayersCount = 0;
        $this->unsoldPlayersCount = 0;
        $this->remainingUnsoldPlayersCount = 0;
        $this->categories = array
        (
            new Category("Keeper", "Indian", true),
            new Category("Keeper", "Overseas", true),
            new Category("All Rounder", "Indian", true),
            new Category("All Rounder", "Overseas", true),
            new Category("Batsman", "Indian", true),
            new Category("Batsman", "Overseas", true),
            new Category("Bowler", "Indian", true),
            new Category("Bowler", "Overseas", true),

            new Category("All Rounder", "Indian", false),
            new Category("All Rounder", "Overseas", false),
            new Category("Batsman", "Indian", false),
            new Category("Batsman", "Overseas", false),
            new Category("Bowler", "Indian", false),
            new Category("Bowler", "Overseas", false),
            new Category("Keeper", "Indian", false),
            new Category("Keeper", "Overseas", false),
        );
    }
}

class Player
{
    public $id;
    public $name;
    public $role;
    public $basePrice;
    public $iplTeam;
    public $nationality;
    public $isStar;
}

class AuctionTeam
{
    public $id;
    public $name;
    public $budgetLeft;
    public $actions; //Array of Action objects
}

class Action
{
    public $leagueTeamId;
    public $playerId;
    public $bid;
}

class Category
{
    public $role;
    public $nationality;
    public $isStar;

    public function __construct($role, $nationality, $isStar)
    {
        $this->role = $role;
        $this->nationality = $nationality;
        $this->isStar = $isStar;
    }
}

class Rule
{
    public $id;
    public $parameter;
    public $type;
    public $value;
    public $lowerBound;
    public $upperBound;
    public $points;
    public $overrideRuleId;

    public function __construct()
    {
        $this->id = null;
        $this->parameter = null;
        $this->type = null;
        $this->value = null;
        $this->lowerBound = null;
        $this->upperBound = null;
        $this->points = null;
        $this->overrideRuleId = null;
    }
}

class Parameter
{
    public $name;
    public $displayName;
    public $ruleIds;

    public function __construct($name, $displayName)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->ruleIds = array();
    }
}

class TournamentPlayer
{
    public $id;
    public $name;
    public $cricketTeam;
    public $points;
    public $role;

    public function __construct()
    {
        $this->points = 0;
    }
}

class LeaguePlayer
{
    public $id;
    public $points;
    public $isActive;

    public function __construct()
    {
        $this->points = 0;
        $this->isActive = true;
    }
}

class LeagueTeam
{
    public $id;
    public $ownerName;
    public $teamName;
    public $leaguePlayers;
    public $curCaptainId;
    public $points;

    public function __construct()
    {
        $this->leaguePlayers = array();
        $this->points = 0;
    }
}

class League
{
    public $id;
    public $name;
    public $leagueTeams;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name= $name;
        $this->leagueTeams = array();
    }
}

class TournamentState
{
    public $matchNo;
    public $leagues;
    public $scoringRules;
    public $players;

    public function __construct()
    {
        $this->matchNo = 0;
        $this->leagues = array();
        $this->players = array();
    }
}