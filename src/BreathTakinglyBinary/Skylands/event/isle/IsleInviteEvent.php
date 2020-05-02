<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\event\isle;


use BreathTakinglyBinary\Skylands\event\Skylands;
use BreathTakinglyBinary\Skylands\isle\IsleHelperInvite;
use pocketmine\Player;

abstract class IsleInviteEvent extends Skylands{

    /** @var IsleHelperInvite */
    private $invite;

    public function __construct(IsleHelperInvite $invite){
        $this->invite = $invite;
    }

    /**
     * @return Player
     */
    public function getInvitee() : Player{
        return $this->invite->getInvitee();
    }

    /**
     * @return string
     */
    public function getIsleOwnerName() : string{
        return $this->invite->getIsleOwnerName();
    }
}