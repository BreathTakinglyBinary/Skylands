<?php


declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\session;

use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Player;
use pocketmine\utils\UUID;

class SkylandsSession {

    /** @var string|null */
    protected $isleId = null;

    /** @var bool */
    protected $loaded = false;

    /** @var string */
    protected $username;

    /** @var UUID */
    protected $uuid;

    /** @var string[]  */
    protected $helperIsles = [];

    /**
     * iSession constructor.
     * @param string $username
     */
    public function __construct(Player $player) {
        $this->username = $player->getName();
        $this->uuid = $player->getUniqueId();
    }

    /**
     * @return UUID
     */
    public function getUuid() : UUID{
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @return null|string
     */
    public function getIsleId(): ?string {
        return $this->isleId;
    }

    /**
     * @return null|Isle
     */
    public function getIsle(): ?Isle {
        if($this->isleId === null){
            return null;
        }
        return Skylands::getInstance()->getIsleManager()->getIsle($this->isleId);
    }

    /**
     * @param null|string $isleId
     */
    public function setIsleId(?string $isleId): void {
        if(!$this->getIsle() instanceof Isle){
            Skylands::getInstance()->getProvider()->loadIsle($isleId);
        }
        $this->isleId = $isleId;
    }

    /**
     * @return bool
     */
    public function hasIsle(): bool {
        return $this->isleId !== null;
    }

    public function save(): void {
        Skylands::getInstance()->getProvider()->saveSession($this);
    }

    public function getHelperIsles() : array{
        return $this->helperIsles;
    }

    public function addHelperIsle(string $isleId) : void{
        //TODO: Add Validation Here
        $this->helperIsles[$isleId] = $isleId;
    }

    public function removeHelperIsle(string $isleId) : void{
        unset($this->helperIsles[$isleId]);
    }

    public function setHelperIsles(array $isles) : void{
        //TODO: Add Validation Here
        $this->helperIsles = $isles;
    }
}