<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class IsleType{

    private $name;

    /** @var Vector3 */
    private $spawn;

    /** @var Vector3 */
    private $chestLocation;

    /** @var array <chunkhash>*/
    private $chunks;

    public function __construct(string $name, Vector3 $spawnLocation, Vector3 $chestLocation, array $chunks = []){
        $this->name = $name;
        $this->spawn = $spawnLocation;
        $this->chestLocation = $chestLocation;
        // TODO: Verify chunk coordinates.
        $this->chunks = $chunks;
    }

    /**
     * @return string
     */
    public function getName() : string{
        return $this->name;
    }

    /**
     * @return Vector3
     */
    public function getSpawn() : Vector3{
        return $this->spawn;
    }

    /**
     * @return Vector3
     */
    public function getChestLocation() : Vector3{
        return $this->chestLocation;
    }

    /**
     * @param string $chunkHash
     *
     * @return Chunk|null
     */
    public function getChunk(int $x, int $z) : ?Chunk{
        $chunkHash = Level::chunkHash($x, $z);
        return isset($this->chunks[$chunkHash]) ? Chunk::fastDeserialize($this->chunks[$chunkHash]) : null;
    }

    /**
     * @param array $chunks
     */
    public function setChunks(array $chunks) : void{
        $this->chunks = $chunks;
    }

}