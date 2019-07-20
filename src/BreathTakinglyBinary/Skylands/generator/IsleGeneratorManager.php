<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\generator;

use pocketmine\level\generator\GeneratorManager as GManager;
use BreathTakinglyBinary\Skylands\generator\presets\BasicIsland;
use BreathTakinglyBinary\Skylands\generator\presets\OPIsland;
use BreathTakinglyBinary\Skylands\generator\presets\PalmIsland;
use BreathTakinglyBinary\Skylands\generator\presets\ShellyGenerator;
use BreathTakinglyBinary\Skylands\Skylands;

class IsleGeneratorManager {

    /** @var Skylands */
    private $plugin;

    /** @var string[] */
    private $generators = [
        "basic" => BasicIsland::class,
        "op" => OPIsland::class,
        "shelly" => ShellyGenerator::class,
        "palm" => PalmIsland::class
    ];
    
    /**
     * GeneratorManager constructor.
     *
     * @param Skylands $plugin
     */
    public function __construct(Skylands $plugin) {
        $this->plugin = $plugin;
        foreach($this->generators as $name => $class) {
            GManager::addGenerator($class, $name);
        }
    }
    
    /**
     * @return string[]
     */
    public function getGenerators(): array {
        return $this->generators;
    }
    
    /**
     * @param string $name
     * @return null|string
     */
    public function getGenerator(string $name): ?string {
        return $this->generators[strtolower($name)] ?? null;
    }

    /**
     * Return if a generator exists
     *
     * @param string $name
     * @return bool
     */
    public function isGenerator(string $name): bool {
        return isset($this->generators[strtolower($name)]);
    }
    
    /**
     * @param string $name
     * @param string $class
     */
    public function registerGenerator(string $name, string $class): void {
        GManager::addGenerator($class, $name);
        if(isset($this->generators[$name])) {
            $this->plugin->getLogger()->debug("Overwriting generator: $name");
        }
        $this->generators[$name] = $class;
    }

}