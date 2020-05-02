<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands;


use BreathTakinglyBinary\Skylands\isle\IsleType;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\item\Item;

class SkylandsSettings {

    /** @var SkylandsSettings */
    private static $instance;
    
    /** @var array */
    private $data;
    
    /** @var Item[] */
    private $defaultChest;
    
    /** @var array */
    private $chestPerIsleType;
    
    /** @var string[] */
    private $messages = [];

    /** @var int */
    private $cooldownDuration;

    /** @var int */
    private $maxHelperIsles = 3;
    
    /**
     * SkyBlockSettings constructor.
     */
    public function __construct() {
        $this->refresh();
        self::$instance = $this;
    }

    /**
     * @return SkylandsSettings
     */
    public static function getInstance() : SkylandsSettings{
        return self::$instance;
    }
    
    /**
     * @param IsleType $isleType
     *
     * @return array
     */
    public function getChestPerIsleType(IsleType $isleType): array {
        return $this->chestPerIsleType[$isleType->getName()] ?? $this->defaultChest;
    }
    
    /**
     * @return string[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @return int
     */
    public function getCooldownDuration(): int {
        return $this->cooldownDuration;
    }

    /**
     * @param string $identifier
     * @param array $args
     * @return string
     */
    public function getMessage(string $identifier, array $args = []): string {
        $message = $this->messages[$identifier] ?? "Message ($identifier) not found";
        $message = Skylands::translateColors($message);
        foreach($args as $arg => $value) {
            $message = str_replace("{" . $arg . "}", $value, $message);
        }
        return $message;
    }

    public function refresh(): void {
        $this->data = json_decode(file_get_contents(Skylands::getInstance()->getDataFolder() . "settings.json"), true);
        $messages = json_decode(file_get_contents(Skylands::getInstance()->getDataFolder() . "messages.json"), true);
        foreach ($messages as $identifier => $message){
            TranslationManager::registerMessage($identifier, $message);
        }
        $this->defaultChest = Skylands::parseItems($this->data["default-chest"]);
        $this->chestPerIsleType = [];
        foreach($this->data["chest-per-isle-type"] as $world => $items) {
            $this->chestPerIsleType[$world] = Skylands::parseItems($items);
        }
        $this->cooldownDuration = $this->data["cooldown-duration-minutes"] ?? 20;
        $this->preventVoidDamage = $this->data["prevent-void-damage"] ?? true;
    }

    /**
     * @return int
     */
    public function getMaxHelperIsles() : int{
        return $this->maxHelperIsles;
    }
    
}