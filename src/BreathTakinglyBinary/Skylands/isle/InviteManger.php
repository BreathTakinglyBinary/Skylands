<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use BreathTakinglyBinary\Skylands\event\isle\IsleInviteAcceptedEvent;
use BreathTakinglyBinary\Skylands\event\isle\IsleInviteDeniedEvent;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

class InviteManger implements Listener{

    /** @var IsleHelperInvite[][] <string, <string, <IsleHelperInvite>> */
    private $invitationsByOwner = [];

    /** @var IsleHelperInvite[][] <string, <string, <IsleHelperInvite>> */
    private $invitationsByInvitee = [];


    public function __construct(){
        Server::getInstance()->getPluginManager()->registerEvents($this, Skylands::getInstance());
    }

    public function onAcceptance(IsleInviteAcceptedEvent $event) : void{
        $this->removeInvitation($event->getInvitee()->getName(), $event->getIsleOwnerName());
    }

    public function onDenial(IsleInviteDeniedEvent $event) : void{
        $this->removeInvitation($event->getInvitee()->getName(), $event->getIsleOwnerName());
    }

    public function addInvitation(Player $isleOwner, Player $invitee) : bool{
        $existing = $this->invitationsByOwner[$isleOwner->getName()][$invitee->getName()] ?? null;
        if($existing instanceof IsleHelperInvite){
            if(!$existing->isExpired()){
                return false;
            }
            $this->removeInvitation($invitee->getName(), $isleOwner->getName());
        }
        $this->invitationsByOwner[$isleOwner->getName()][$invitee->getName()] = new IsleHelperInvite($isleOwner, $invitee);
        $this->invitationsByInvitee[$invitee->getName()][$isleOwner->getName()] = $this->invitationsByOwner[$isleOwner->getName()][$invitee->getName()];
        return true;
    }

    public function removeInvitation(string $inviteeName, string $isleOwner) : void{
        unset($this->invitationsByOwner[$isleOwner][$inviteeName], $this->invitationsByInvitee[$inviteeName][$isleOwner]);
    }

    /**
     * @param Player $invitee
     *
     * @return IsleHelperInvite[] <string, IsleHelperInvite>
     */
    public function getInvitationsByInvitee(Player $invitee) : array{
        return $this->invitationsByInvitee[$invitee->getName()] ?? [];
    }

    public function getInvitationsByIsleOwner(Player $isleOwner) : array{
        return $this->invitationsByOwner[$isleOwner->getName()] ?? [];
    }

}