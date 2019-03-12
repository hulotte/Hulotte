<?php

namespace Hulotte\Services;

use Exception;

/**
 * Class PasswordService
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PasswordService
{
    private $symbolTypes = [
        'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
        'number' => '0123456789',
        'special' => ',?;.:/!&#-_@',
    ];

    /**
     * Generate password with constraints:
     * @param int $length
     * @param array $selectedTypes
     * @return string
     * @throws Exception
     */
    public function createPassword(int $length = 12, array $selectedTypes = []): string
    {
        if ($length < 6) {
            throw new Exception('Le mot de passe doit au moins faire 6 caractères.');
        } elseif (!$this->isTypesExist($selectedTypes)) {
            throw new Exception('Un type de symbole n\'existe pas.');
        }

        if (empty($selectedTypes)) {
            $selectedTypes = array_keys($this->symbolTypes);
        }

        $password = $this->addMandatorySymbols($selectedTypes);
        $remaningNbr = $length - mb_strlen($password);
        $chars = $this->concatenateSelectedSymbols($selectedTypes);
        $charsCount = mb_strlen($chars);

        for ($i = 0; $i < $remaningNbr; $i++) {
            $position = rand(0, $charsCount - 1);
            $password .= $chars[$position];
        }

        return str_shuffle($password);
    }

    /**
     * Fill password with mandatory symbol's types
     * @param string[] $selectedTypes
     * @return string
     */
    private function addMandatorySymbols(array $selectedTypes = []): string
    {
        $password = '';

        foreach ($selectedTypes as $type) {
            $position = rand(0, mb_strlen($this->symbolTypes[$type]) - 1);
            $password .= $this->symbolTypes[$type][$position];
        }

        return str_shuffle($password);
    }

    /**
     * Assemble wanted symbols
     * @param string[] $selectedTypes
     * @return string
     */
    private function concatenateSelectedSymbols(array $selectedTypes): string
    {
        $symbols = array_map(function ($symbol) {
            return $this->symbolTypes[$symbol];
        }, $selectedTypes);

        return implode('', $symbols);
    }

    /**
     * Verify if the symbole types exists
     * @param string[] $selectedTypes
     * @return bool
     */
    private function isTypesExist(array $selectedTypes): bool
    {
        foreach ($selectedTypes as $type) {
            if (!array_key_exists($type, $this->symbolTypes)) {
                return false;
            }
        }

        return true;
    }
}
