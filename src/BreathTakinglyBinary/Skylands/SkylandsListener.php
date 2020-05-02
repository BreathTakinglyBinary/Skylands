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
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\session\SkylandsSession;
use pocketmine\block\Solid;
use pocketmine\entity\object\Painting;
use pocketmine\entity\projectile\Projectile;
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
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use BreathTakinglyBinary\Skylands\generator\IsleGenerator;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\session\SessionManager;

class SkylandsListener implements Listener {

    public function onJoin(PlayerJoinEvent $event){
        SessionManager::getInstance()->openSession($event->getPlayer());
    }

    public function onLevelChange(EntityLevelChangeEvent $event) : void{
        $player = $event->getEntity();
        if(!$player instanceof Player){
            return;
        }

        $session = SessionManager::getInstance()->getSession($player);
        if(!$session instanceof SkylandsSession){
            return;
        }
        $isle = $session->getIsle();
        if(!$isle instanceof Isle){
            return;
        }
        if($isle->getLevel()->getId() === $event->getTarget()->getId()){
            $player->setGamemode(Player::SURVIVAL);
        }
    }

    private function isInteractionAllowed(Player $player) : bool{
        $isle = IsleManager::getInstance()->getIsle($player->getLevel()->getName());
        if(($isle !== null) && !$isle->canInteract($player)){
            return false;
        }
        return true;
    }
    
    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isInteractionAllowed($player)){
            $player->sendPopup(TranslationManager::getTranslatedMessage("MUST_BE_MEMBER"));
            $event->setCancelled();
        }
    }
    
    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isInteractionAllowed($player)){
            $player->sendPopup(TranslationManager::getTranslatedMessage("MUST_BE_MEMBER"));
            $event->setCancelled();
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->isInteractionAllowed($player)) {
            $player->sendPopup(TranslationManager::getTranslatedMessage("MUST_BE_MEMBER"));
            $event->setCancelled();
        }
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function onHurt(EntityDamageEvent $event): void {
        $target = $event->getEntity();
        $level = $target->getLevel();
        if(!$target instanceof Player){
            return;
        }

        if($level === null){
            return;
        }

        $isle = IsleManager::getInstance()->getIsle($level->getName());
        if($isle === null){
            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if($damager instanceof Projectile){
                $damager = $damager->getOwningEntity();
            }
            if(!$damager instanceof Player){
                return;
            }
            if(!$target instanceof Player and !$target instanceof Painting){
                return;
            }
            if(!$this->isInteractionAllowed($damager)) {
                $event->setCancelled();
            }
        }
    }
    
    
    /**
     * @param LevelUnloadEvent $event
     */
    public function onUnloadLevel(LevelUnloadEvent $event): void {
        foreach($event->getLevel()->getPlayers() as $player) {
            $player->teleport(Skylands::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $isle = SessionManager::getInstance()->getSession($player)->getIsle();
        if($isle !== null) {
            IsleManager::getInstance()->closeIsle($isle);
        }
        SessionManager::getInstance()->closeSession($player);
    }

    public function onLevelLoad(LevelLoadEvent $event): void {
        Skylands::getInstance()->getProvider()->loadIsle($event->getLevel()->getFolderName());
    }

}