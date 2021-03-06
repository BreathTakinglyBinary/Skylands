<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands;

use BreathTakinglyBinary\Skylands\isle\Isle;
use pocketmine\block\Solid;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use BreathTakinglyBinary\Skylands\generator\IsleGenerator;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\session\SessionManager;

class SkylandsListener implements Listener {

    /** @var Skylands */
    private $plugin;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IsleManager */
    private $isleManager;

    /**
     * SkyBlockListener constructor.
     *
     * @param Skylands $plugin
     */
    public function __construct(Skylands $plugin) {
        $this->plugin = $plugin;
        $this->sessionManager = $plugin->getSessionManager();
        $this->isleManager = $plugin->getIsleManager();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param Player $player
     * @return Session|null
     */
    public function getSession(Player $player): ?Session {
        return $this->plugin->getSessionManager()->getSession($player);
    }
    
    /**
     * @param ChunkLoadEvent $event
     */
    public function onChunkLoad(ChunkLoadEvent $event): void {
        $level = $event->getLevel();
        $isle = $this->plugin->getIsleManager()->getIsle($level->getName());
        if($isle === null) {
            return;
        }
        $generator = $this->plugin->getGeneratorManager()->getGenerator($type = $isle->getType());
        /** @var IsleGenerator $generator */
        $position = $generator::getChestPosition();
        if($level->getChunk($position->x >> 4, $position->z >> 4) === $event->getChunk() and $event->isNewChunk()) {
            /** @var Chest $chest */
            $chest = Tile::createTile(Tile::CHEST, $level, Chest::createNBT($position));
            foreach($this->plugin->getSettings()->getChestPerGenerator($type) as $item) {
                $chest->getInventory()->addItem($item);
            }
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event) : void{
        $player = $event->getEntity();
        if(!$player instanceof Player){
            return;
        }

        $session = $this->getSession($player);
        if(!$session instanceof Session){
            return;
        }
        $isle = $session->getIsle();
        if(!$isle instanceof Isle){
            return;
        }
        if($session->getIsle()->getLevel()->getId() === $event->getTarget()->getId()){
            $player->setGamemode(Player::SURVIVAL);
        }
    }
    
    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->isleManager->getIsle($player->getLevel()->getName());
        if($isle !== null) {
            if(!$isle->canInteract($session)) {
                $session->sendTranslatedPopup("MUST_ME_MEMBER");
                $event->setCancelled();
            } elseif(!($event->isCancelled()) and $event->getBlock() instanceof Solid) {
                $isle->destroyBlock();
            }
        }
    }
    
    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->isleManager->getIsle($player->getLevel()->getName());
        if($isle !== null) {
            if(!$isle->canInteract($session)) {
                $session->sendTranslatedPopup("MUST_ME_MEMBER");
                $event->setCancelled();
            } elseif(!($event->isCancelled()) and $event->getBlock() instanceof Solid) {
                $isle->addBlock();
            }
        }
    }

    /**
     * @param BlockFormEvent $event
     */
    public function onBlockForm(BlockFormEvent $event): void {
        $block = $event->getBlock();
        $newBlock = $event->getNewState();
        $isle = $this->isleManager->getIsle($block->getLevel()->getName());
        if($isle !== null and !($block instanceof Solid) and $newBlock instanceof Solid) {
            $isle->addBlock();
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->plugin->getIsleManager()->getIsle($player->getLevel()->getName());
        if($isle !== null and !($isle->canInteract($session))) {
            $session->sendTranslatedPopup("MUST_ME_MEMBER");
            $event->setCancelled();
        }
    }
    
    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event): void {
        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($event->getPlayer());
        if(!($session->hasIsle()) or !($session->isInChat())) {
            return;
        }
        $recipients = [];
        foreach($sessionManager->getSessions() as $userSession) {
            if($userSession->isInChat() and $userSession->getIsle() === $session->getIsle()) {
                $recipients[] = $userSession->getPlayer();
            }
        }
        $event->setRecipients($recipients);
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function onHurt(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();
        if(!$entity instanceof Player){
            return;
        }

        if($level === null){
            return;
        }

        $isle = $this->isleManager->getIsle($level->getName());
        if($isle === null){
            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if(($entity instanceof Player or ($entity instanceof Painting and $damager instanceof Player
                and !$isle->canInteract($this->getSession($damager))))) {
                $event->setCancelled();
            }
        } elseif($event->getCause() === EntityDamageEvent::CAUSE_VOID
            and $this->plugin->getSettings()->isPreventVoidDamage()) {
            $entity->teleport($isle->getSpawnLocation());
            $event->setCancelled();
        }
    }
    
    
    /**
     * @param LevelUnloadEvent $event
     */
    public function onUnloadLevel(LevelUnloadEvent $event): void {
        foreach($event->getLevel()->getPlayers() as $player) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onCommand(PlayerCommandPreprocessEvent $event): void {
        $message = $event->getMessage();
        $player = $event->getPlayer();
        if($this->isleManager->getIsle($player->getLevel()->getName()) !== null and
            $message{0} === "/" and
            in_array(strtolower(substr($message, 1)), $this->plugin->getSettings()->getIsleBlockedCommands())
        ) {
            $this->getSession($player)->sendTranslatedMessage("BLOCKED_COMMAND");
            $event->setCancelled();
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @throws \ReflectionException
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        if($session === null) return;
        $isleManager = $this->plugin->getIsleManager();
        foreach($isleManager->getIsles() as $isle) {
            if($isle->isCooperator($session)) {
                $isle->removeCooperator($session);
            }
        }
        $isle = $session->getIsle();
        if($isle !== null) {
            if(empty($isle->getMembersOnline())){
                foreach($isle->getLevel()->getPlayers() as $nonMember){
                    $nonMember->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
                    $this->getSession($nonMember)->sendTranslatedMessage("ISLE_CLOSING");
                }
                $isle->tryToClose();
            }
        }
    }

    public function onLevelLoad(LevelLoadEvent $event): void {
        $this->plugin->getProvider()->loadIsle($event->getLevel()->getFolderName());
    }

}