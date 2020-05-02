<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use BreathTakinglyBinary\Skylands\event\isle\IsleInviteAcceptedEvent;
use BreathTakinglyBinary\Skylands\event\isle\IsleInviteDeniedEvent;
use pocketmine\Player;

class IsleHelperInvite{

    const PENDING = 0;
    const ACCEPTED = 1;
    const DENIED = 2;


    /** @var Player */
    private $invitee;

    /** @var string */
    private $isleId;

    /** @var string */
    private $isleName;

    /** @var string */
    private $isleOwnerName;

    /** @var int */
    private $status = self::PENDING;

    /** @var \DateTime */
    private $timeCreated;

    public function __construct(Player $isleOwner, Player $invitee){
        $this->isleOwnerName = $isleOwner->getName();
        $this->invitee = $invitee;
        $this->timeCreated = new \DateTime();
    }

    /**
     * @return string
     */
    public function getIsleId() : string{
        return $this->isleId;
    }

    /**
     * @return string
     */
    public function getIsleName() : string{
        return $this->isleName;
    }

    /**
     * @return string
     */
    public function getIsleOwnerName() : string{
        return $this->isleOwnerName;
    }

    /**
     * @return Player
     */
    public function getInvitee() : Player{
        return $this->invitee;
    }

    public function isExpired() : bool{
        $test = new \DateTime();
        $test->sub(new \DateInterval("PT2M"));
        return $test >= $this->timeCreated;
    }

    public function accept() : void{
        $this->status = self::ACCEPTED;
        (new IsleInviteAcceptedEvent($this))->call();
    }

    public function deny() : void{
        $this->status = self::DENIED;
        (new IsleInviteDeniedEvent($this))->call();
    }

    /**
     * @return int
     */
    public function getStatus() : int{
        return $this->status;
    }
}