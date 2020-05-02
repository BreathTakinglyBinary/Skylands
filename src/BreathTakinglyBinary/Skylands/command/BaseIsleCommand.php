<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\command;


use BreathTakinglyBinary\Skylands\command\subcommand\GoSubCommand;
use BreathTakinglyBinary\Skylands\ui\forms\SkylandsMainMenu;
use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BaseIsleCommand extends BaseCommand{

    public function __construct(){
        parent::__construct("isle", "Island Options", ["is", "sb", "skyblock"]);
    }

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare() : void{
        $this->registerSubCommand(new GoSubCommand());
    }

    /**
     * @param CommandSender  $sender
     * @param string         $aliasUsed
     * @param BaseArgument[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage("This command must be used in game.");
        }
        $sender->sendForm(new SkylandsMainMenu($sender));
    }
}