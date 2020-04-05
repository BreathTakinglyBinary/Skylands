<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\utils;


use BreathTakinglyBinary\Skylands\ui\forms\skyblock\MemeberManagementActions;

class SkylandsUtils{

    public static function getMemberManagementActionWord(int $action) : string{
        $actionWord = "";
        switch($action){
            case MemeberManagementActions::ACTION_DEMOTE:
                $actionWord = "DEMOTE";
                break;
            case MemeberManagementActions::ACTION_FIRE:
                $actionWord = "FIRE";
                break;
            case MemeberManagementActions::ACTION_PROMOTE:
                $actionWord = "PROMOTE";
                break;
        }

        return $actionWord;
    }

}