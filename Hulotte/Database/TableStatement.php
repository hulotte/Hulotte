<?php

namespace Hulotte\Database;

/**
 * Trait TableStatement
 * Add methods for basics statements
 *
 * @package Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
trait TableStatement
{
    /**
     * Define the statement to select this table
     * @return StatementBuilder
     */
    public function allStatement(): StatementBuilder
    {
        return (new StatementBuilder())
            ->from($this->table);
    }
    
    /**
     * Define the statement for a count
     * @return StatementBuilder
     */
    public function countStatement(): StatementBuilder
    {
        return $this->allStatement()
            ->select('COUNT(id) as count');
    }
}
