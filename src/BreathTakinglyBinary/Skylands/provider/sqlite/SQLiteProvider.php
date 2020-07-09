<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\provider\sqlite;

use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\provider\Provider;
use BreathTakinglyBinary\Skylands\session\SkylandsSession;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Server;
use pocketmine\utils\UUID;

class SQLiteProvider extends Provider{

    /** @var \SQLite3 */
    private $sqlite;

    public function initialize() : void{
        $this->sqlite = new \SQLite3(Skylands::getInstance()->getDataFolder() . "skylandsData.db");

        $this->sqlite->query("
            CREATE TABLE IF NOT EXISTS players(
                playerUUID BLOB PRIMARY KEY,
                username VARCHAR(32),
                isleID VARCHAR(128)
            )
        ");

        $this->sqlite->query("
            CREATE TABLE IF NOT EXISTS isles(
                isleID VARCHAR(128) PRIMARY KEY,
                ownerUUID BLOB NOT NULL ,
                isleType VARCHAR(64) NOT NULL,
                isleName VARCHAR(64),
                locked BOOLEAN DEFAULT FALSE,
                points INTEGER DEFAULT 0
            )
        ");

        $this->sqlite->query("
            CREATE TABLE IF NOT EXISTS helpers(
                isleID VARCHAR(128) NOT NULL ,
                helperUUID BLOB NOT NULL ,
                UNIQUE (isleID, helperUUID)
            )
        ");
    }

    /**
     * @param SkylandsSession $session
     */
    public function loadSession(SkylandsSession $session) : void{
        Skylands::logger()->debug("SQLiteProvider loading session for " . $session->getUsername());
        $stmt = $this->sqlite->prepare("SELECT * FROM players WHERE playerUUID = :uuid");
        $stmt->bindValue(":uuid", $session->getUuid()->toBinary(), SQLITE3_BLOB);
        $result = $stmt->execute();
        $data = $result->fetchArray();
        $result->finalize();
        Skylands::logger()->debug("Query complete...");
        if(isset($data["isleID"])){
            Skylands::logger()->debug("Found isleID ...");
            $isleId = $data["isleID"];
            if(!Server::getInstance()->isLevelGenerated($isleId)){
                Skylands::logger()->error("Tried to create session for " . $session->getUsername() . " with invalid isleID of $isleId (level not generated).");
                return;
            }
            $session->setIsleId($isleId);
        }

        $stmt2 = $this->sqlite->prepare("SELECT isleID FROM helpers WHERE helperUUID = :uuid");
        $stmt2->bindValue(":uuid", $session->getUuid()->toBinary(), SQLITE3_BLOB);
        $result2 = $stmt2->execute();
        $helperData = $result2->fetchArray();
        $result2->finalize();
        if(is_array($helperData)){
            foreach($helperData as $helperIsleID){
                $session->addHelperIsle($helperIsleID);
            }
        }
    }

    /**
     * @param SkylandsSession $session
     */
    public function saveSession(SkylandsSession $session) : void{
        $stmt = $this->sqlite->prepare("REPLACE INTO players(playerUUID, username, isleID) VALUES (:uuid, :username, :isleID)");
        $stmt->bindValue(":uuid", $session->getUuid()->toBinary(), SQLITE3_BLOB);
        $stmt->bindValue(":username", $session->getUsername());
        $stmt->bindValue(":isleID", $session->getIsleId());
        $stmt->execute();
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function loadIsle(string $identifier) : bool{
        $stmt = $this->sqlite->prepare("
            SELECT * FROM isles WHERE isleID = :isleId
        ");
        $stmt->bindValue(":isleId", $identifier);
        $result = $stmt->execute();
        $isleData = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();

        $stmt2 = $this->sqlite->prepare("
            SELECT * FROM helpers WHERE isleId = :isleId
        ");
        $stmt2->bindValue(":isleId", $identifier);
        $result2 = $stmt2->execute();
        $helperData = $result2->fetchArray(SQLITE3_ASSOC);
        $result2->finalize();

        if(!is_array($isleData)){
            Skylands::logger()->warning("No data found for island $identifier!");
            return false;
        }

        if(!isset($isleData["ownerUUID"])){
            Skylands::logger()->error("Unable to load island $identifier due to invalid ownerUUID!");
            return false;
        }

        $uuid = UUID::fromBinary($isleData["ownerUUID"]);

        $typeName = $isleData["isleType"] ?? "DEFAULT";
        $type = Skylands::getInstance()->getIsleManager()->getIsleTypeByName($typeName);

        $name = $isleData["isleName"] ?? "";

        $locked = $isleData["locked"] ?? true;

        $points = $isleData["points"] ?? 0;

        $helpers = [];
        if(!is_array($helperData)){
            $helperData = [];
        }
        foreach($helperData as $binaryUUID){
            $helpers[] = UUID::fromBinary($binaryUUID);
        }

        $isle = new Isle($uuid, $identifier, $type, $name, (bool) $locked, $helpers, (int) $points);
        Skylands::getInstance()->getIsleManager()->openIsle($isle);
        return true;
    }

    /**
     * @param Isle $isle
     */
    public function saveIsle(Isle $isle) : void{
        $stmt = $this->sqlite->prepare("
            REPLACE INTO isles(isleID, ownerUUID, isleType, isleName, locked, points) VALUES(:id, :uuid, :type, :name, :lock, :points)
        ");
        $stmt->bindValue(":id", $isle->getIdentifier());
        $stmt->bindValue(":uuid", $isle->getOwnerUUID()->toBinary(), SQLITE3_BLOB);
        $stmt->bindValue(":type", $isle->getType()->getName());
        $stmt->bindValue(":name", $isle->getName());
        $stmt->bindValue(":lock", $isle->isLocked());
        $stmt->bindValue(":points", $isle->getPoints());
        ($stmt->execute())->finalize();


        foreach($isle->getHelpers() as $helper){
            unset($helperStmt);
            $helperStmt = $this->sqlite->prepare("
                INSERT OR IGNORE INTO helpers(isleID, helperUUID) VALUES (:id, :uuid)
            ");
            $helperStmt->bindValue(":id", $isle->getIdentifier());
            $helperStmt->bindValue(":uuid", $helper->toBinary(), SQLITE3_BLOB);
            ($helperStmt->execute())->finalize();
        }

    }
}