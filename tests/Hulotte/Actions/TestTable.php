<?php

namespace Tests\Hulotte\Actions;

use Hulotte\{
    Database\Table,
    Modules\Blog\Entity\CategoryEntity
};

/**
 * Class TestTable
 *
 * @package Tests\Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TestTable extends Table
{
    /**
     * @var string
     */
    protected $entity = TestEntity::class;

    /**
     * @var string
     */
    protected $table = 'test';
}
