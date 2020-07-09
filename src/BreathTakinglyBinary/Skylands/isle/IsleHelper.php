<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use pocketmine\utils\UUID;

class IsleHelper{

    /** @var UUID */
    private $uuid;

    /** @var string */
    private $displayName;

    /** @var string */
    private $name;

    public function __construct(UUID $uuid, string $name, ?string $displayName = null){
        $this->uuid = $uuid;
        $this->name = $name;
        $this->displayName = $this->displayName ?? $name;
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
    public function getName() : string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void{
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName() : string{
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName) : void{
        $this->displayName = $displayName;
    }

}