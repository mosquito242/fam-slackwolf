<?php namespace Slackwolf\Game\Command;

use Exception;
use Slackwolf\Game\Formatter\UserIdFormatter;
use Slackwolf\Game\GameState;
use Zend\Loader\Exception\InvalidArgumentException;

class VoteCommand extends Command
{
    private $game;

    public function init()
    {
        if ($this->channel[0] == 'D') {
            throw new Exception("You may not !vote privately.");
        }

        if (count($this->args) < 1) {
            throw new InvalidArgumentException("Must specify a player");
        }

        $this->args[0] = UserIdFormatter::format($this->args[0]);

        $this->game = $this->gameManager->getGame($this->channel);

        if ( ! $this->game) {
            throw new Exception("No game in progress.");
        }

        if ($this->game->getState() != GameState::DAY) {
            throw new Exception("Voting occurs only during the day.");
        }

        // Voter should be alive
        if ( ! $this->game->hasPlayer($this->userId)) {
            throw new Exception("Can't vote if dead.");
        }

        // Person player is voting for should also be alive
        if ( ! $this->game->hasPlayer($this->args[0])) {
            throw new Exception("Voted player not found in game.");
        }
    }

    public function fire()
    {
        $this->gameManager->vote($this->game, $this->userId, $this->args[0]);
    }
}