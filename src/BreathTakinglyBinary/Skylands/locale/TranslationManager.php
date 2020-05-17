<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\locale;


use http\Exception\RuntimeException;
use pocketmine\utils\TextFormat;

class TranslationManager{

    private static $colorKey = [
        "&" => TextFormat::ESCAPE,
        "BLACK" => TextFormat::BLACK,
        "DARK_BLUE" => TextFormat::DARK_BLUE,
        "DARK_GREEN" => TextFormat::DARK_GREEN,
        "DARK_AQUA" => TextFormat::DARK_AQUA,
        "DARK_RED" => TextFormat::DARK_RED,
        "DARK_PURPLE" => TextFormat::DARK_PURPLE,
        "DARK_GRAY" => TextFormat::DARK_GRAY,
        "ORANGE" => TextFormat::GOLD,
        "GOLD" => TextFormat::GOLD,
        "GRAY" => TextFormat::GRAY,
        "BLUE" => TextFormat::BLUE,
        "GREEN" => TextFormat::GREEN,
        "AQUA" => TextFormat::AQUA,
        "RED" => TextFormat::RED,
        "LIGHT_PURPLE" => TextFormat::LIGHT_PURPLE,
        "YELLOW" => TextFormat::YELLOW,
        "WHITE" => TextFormat::WHITE,
        "OBFUSCATED" => TextFormat::OBFUSCATED,
        "BOLD" => TextFormat::BOLD,
        "STRIKETHROUGH" => TextFormat::STRIKETHROUGH,
        "UNDERLINE" => TextFormat::UNDERLINE,
        "ITALIC" => TextFormat::ITALIC,
        "RESET" => TextFormat::RESET
    ];

    /** @var String[] */
    private static $message = [];

    public static function registerMessage(string $id, string $message) : void{
        if(isset(self::$message[$id])){
            throw new \InvalidArgumentException("Tried to register message with ID $id when it has already been registered!");
        }
        self::$message[$id] = self::convertColors($message);
    }

    private static function convertColors(string $text) : string{
        return self::textUpdateFromArgs($text, self::$colorKey);
    }

    public static function getTranslatedMessage(string $id, array $args = []) : ?string{
        return self::textUpdateFromArgs(self::$message[$id] ?? "", $args);
    }

    private static function textUpdateFromArgs(string $text, array $args) : string{
        foreach($args as $k => $v){
            $newMsg = \str_replace("{" . $k . "}", $v, $text);
            if(!is_string($newMsg)){
                throw new RuntimeException("TranslationManager::getTextUpdateFromArgs() got an unexpected value when trying to replace execute str_replace() using $k with $v in message \"$text\"");
            }
            $text = $newMsg;
            unset($newMsg);
        }
        return $text;
    }

}