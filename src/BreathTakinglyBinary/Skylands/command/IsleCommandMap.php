<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\command;


use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\ui\forms\skyblock\NoIslandMenu;
use BreathTakinglyBinary\Skylands\ui\forms\skyblock\SkyBlockMainMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use BreathTakinglyBinary\Skylands\command\presets\AcceptCommand;
use BreathTakinglyBinary\Skylands\command\presets\ChatCommand;
use BreathTakinglyBinary\Skylands\command\presets\CooperateCommand;
use BreathTakinglyBinary\Skylands\command\presets\CreateCommand;
use BreathTakinglyBinary\Skylands\command\presets\DemoteCommand;
use BreathTakinglyBinary\Skylands\command\presets\DenyCommand;
use BreathTakinglyBinary\Skylands\command\presets\DisbandCommand;
use BreathTakinglyBinary\Skylands\command\presets\FireCommand;
use BreathTakinglyBinary\Skylands\command\presets\HelpCommand;
use BreathTakinglyBinary\Skylands\command\presets\InviteCommand;
use BreathTakinglyBinary\Skylands\command\presets\JoinCommand;
use BreathTakinglyBinary\Skylands\command\presets\KickCommand;
use BreathTakinglyBinary\Skylands\command\presets\LeaveCommand;
use BreathTakinglyBinary\Skylands\command\presets\LockCommand;
use BreathTakinglyBinary\Skylands\command\presets\MembersCommand;
use BreathTakinglyBinary\Skylands\command\presets\PromoteCommand;
use BreathTakinglyBinary\Skylands\command\presets\SetSpawnCommand;
use BreathTakinglyBinary\Skylands\command\presets\TransferCommand;
use BreathTakinglyBinary\Skylands\command\presets\VisitCommand;
use BreathTakinglyBinary\Skylands\Skylands;

class IsleCommandMap extends Command implements PluginIdentifiableCommand {
    
    /** @var IsleCommand[] */
    private $commands = [];

    public function __construct() {
        $this->registerCommand(new HelpCommand($this));
        $this->registerCommand(new CreateCommand());
        $this->registerCommand(new JoinCommand());
        $this->registerCommand(new LockCommand());
        $this->registerCommand(new ChatCommand());
        $this->registerCommand(new VisitCommand());
        $this->registerCommand(new LeaveCommand());
        $this->registerCommand(new MembersCommand());
        $this->registerCommand(new InviteCommand());
        $this->registerCommand(new AcceptCommand());
        $this->registerCommand(new DenyCommand());
        $this->registerCommand(new DisbandCommand());
        $this->registerCommand(new KickCommand());
        $this->registerCommand(new FireCommand());
        $this->registerCommand(new PromoteCommand());
        $this->registerCommand(new DemoteCommand());
        $this->registerCommand(new SetSpawnCommand());
        $this->registerCommand(new TransferCommand());
        $this->registerCommand(new CooperateCommand());
        parent::__construct("isle", "SkyBlock command", "Usage: /is", [
            "island",
            "is",
            "isle",
            "sb",
            "skyblock"
        ]);
        Skylands::getInstance()->getServer()->getCommandMap()->register("skyblock", $this);
    }

    /**
     * @return Skylands|Plugin
     */
    public function getPlugin(): Plugin {
        return Skylands::getInstance();
    }
    
    /**
     * @return IsleCommand[]
     */
    public function getCommands(): array {
        return $this->commands;
    }
    
    /**
     * @param string $alias
     * @return null|IsleCommand
     */
    public function getCommand(string $alias): ?IsleCommand {
        foreach($this->commands as $key => $command) {
            if(in_array(strtolower($alias), $command->getAliases()) or $alias === $command->getName()) {
                return $command;
            }
        }
        return null;
    }
    
    /**
     * @param IsleCommand $command
     */
    public function registerCommand(IsleCommand $command) {
        $this->commands[] = $command;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game");
            return;
        }
        
        $session = Skylands::getInstance()->getSessionManager()->getSession($sender);
        if(isset($args[0]) and $this->getCommand($args[0]) !== null) {
            $this->getCommand(array_shift($args))->onCommand($session, $args);
        } else {
            if(Skylands::getInstance()->getSessionManager()->getSession($sender)->getIsle() instanceof Isle){
                $sender->sendForm(new SkyBlockMainMenu());
                return;
            }
            $sender->sendForm(new NoIslandMenu());
        }
    }
    
}