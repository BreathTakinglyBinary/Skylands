<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\Skylands;
use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;

class ViewIslandInvitesMenu extends SimpleForm{

    /** @var Isle[]|null */
    private $invites;

    public function __construct(Session $session){
        parent::__construct();
        $this->setTitle("Island Invitations");
        $this->invites = $session->getInvitations();
        foreach($this->invites as $sender => $invitation){
            $this->addButton($sender, $sender);
        }
        $this->addButton("Back", "back");
    }

    protected function onSubmit(Player $player, $data){
        if($data === "back"){
            $player->sendForm(new NoIslandMenu());
            return;
        }
        $session = Skylands::getInstance()->getSessionManager()->getSession($player);
        if(isset($this->invites[$data])){
            $isle = $this->invites[$data];
            $session->setLastInvitation(null);
            $session->removeInvitation($data);
            $session->setRank(BaseSession::RANK_DEFAULT);
            $session->setIsle($isle);
            $isle->broadcastTranslatedMessage("PLAYER_JOINED_THE_ISLE", [
                "name" => $session->getUsername()
            ]);
        }
        $player->sendForm(new NoIslandMenu("Invite from $data has expired."));
    }
}