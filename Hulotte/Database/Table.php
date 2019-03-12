<?php

namespace Hulotte\Database;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Database\TableStatement,
    Services\Paginator
};

/**
 * Class Table
 *
 * @package Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Table
{
    use TableStatement;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * Table constructor
     * @param ContainerInterface $container
     * @param Database $database
     */
    public function __construct(ContainerInterface $container, Database $database)
    {
        $this->container = $container;
        $this->database = $database;
    }

    /**
     * Get all items on a table
     * @return mixed
     */
    public function all()
    {
        return $this->query((string)$this->allStatement());
    }

    /**
     * Get all items on a table from a specific key
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function allBy(string $key, $value)
    {
        $statement = $this->allStatement()
            ->where("$key = :$key");

        return $this->query((string)$statement, [":$key" => $value]);
    }

    /**
     * Get all items on array
     * @example ->allList('id', 'label')
     * @param string $firstKey
     * @param null|string $secondKey
     * @param null|string $statement
     * @return null|array
     */
    public function allList(string $firstKey, ?string $secondKey = null, ?string $statement = null): ?array
    {
        if ($secondKey) {
            $select = [$firstKey, $secondKey];
        } else {
            $select = $firstKey;
        }

        if (!$statement) {
            $statement = $this->allStatement()
                ->select($select);
        }

        $records = $this->query((string)$statement);
        $results = [];

        if (!$records) {
            return null;
        }

        foreach ($records as $record) {
            if ($secondKey) {
                if ($this->entity) {
                    $results[$record->$firstKey] = $record->$secondKey;
                } else {
                    $results[$record[$firstKey]] = $record[$secondKey];
                }
            } else {
                if ($this->entity) {
                    $results[] = $record->$firstKey;
                } else {
                    $results[] = $record[$firstKey];
                }
            }
        }

        return $results;
    }

    /**
     * Count the items of the table
     * @return mixed
     */
    public function count()
    {
        return $this->query((string)$this->countStatement(), [], true)->count;
    }

    /**
     * create an item on database
     * @param array $values
     * @return mixed
     */
    public function create(array $values)
    {
        $this->insert($values);
    }

    /**
     * Delete an item on database
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $statement = (new StatementBuilder())
            ->delete($this->table)
            ->where('id = :id');

        return $this->query((string)$statement, [':id' => $id], true);
    }

    /**
     * Get an item from its id
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $statement = $this->allStatement()
            ->where('id = :id');

        return $this->query((string)$statement, [':id' => $id], true);
    }

    /**
     * Get an item from a specific key
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function findBy(string $key, $value)
    {
        $statement = $this->allStatement()
            ->where("$key = :$key");

        return $this->query((string)$statement, [":$key" => $value], true);
    }

    /**
     * Entity getter
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Table getter
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Insert an item on database
     * @param array $values
     * @param null|string $table
     * @return mixed
     */
    public function insert(array $values, ?string $table = null)
    {
        $columns = [];
        $attributes = [];
        $preparedValues = [];

        if (!$table) {
            $table = $this->table;
        }

        foreach ($values as $column => $value) {
            $columns[] = $column;
            $attributes[":$column"] = $value;
            $preparedValues[] = ":$column";
        }

        $statement = (new StatementBuilder())
            ->insert($table)
            ->columns($columns)
            ->values($preparedValues);

        return $this->query((string)$statement, $attributes, true);
    }

    /**
     * Verify if an id exists
     * @param string $key
     * @param null|string $value
     * @return bool
     */
    public function isExists(string $key, ?string $value = null): bool
    {
        if (!$value) {
            $value = $key;
            $key = 'id';
        }

        $statement = $this->allStatement()
            ->select($key)
            ->where($key . ' = :' . $key);

        $record = $this->query((string)$statement, [':' . $key => $value], true);

        return $record ? true : false;
    }

    /**
     * Get the last id insert on database
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int)$this->database->lastInsertId();
    }

    /**
     * Get paginated result
     * @param StatementBuilder $statement
     * @param int $perPage
     * @param int $currentPage
     * @param null|array $params
     * @return null|Paginator
     */
    public function paginate(
        StatementBuilder $statement,
        int $perPage,
        int $currentPage = 1,
        ?array $params = null
    ): ?Paginator {
        $countStatement = clone $statement;
        $countStatement = $countStatement->select('COUNT(id) AS count');

        if ($this->entity) {
            $totalItems = $this->query((string)$countStatement, $params, true)->count;
        } else {
            $totalItems = $this->query((string)$countStatement, $params, true)['count'];
        }
        
        $totalPages = ceil($totalItems / $perPage);
        $first = ($currentPage - 1) * $perPage;
        $paginateStatement = $statement->limit($first, $perPage);

        $records = $this->query((string)$paginateStatement, $params);

        if ($records) {
            return new Paginator($records, $currentPage, $totalPages);
        }

        return null;
    }

    /**
     * Make a request in database
     * @param string $statement
     * @param null|array $attributes
     * @param bool $one
     * @return mixed
     */
    public function query(string $statement, ?array $attributes = null, bool $one = false)
    {
        if ($attributes) {
            $records = $this->database->prepare($statement, $attributes, $one);
        } else {
            $records = $this->database->query($statement, $one);
        }

        if (strpos($statement, 'UPDATE') !== 0
            && strpos($statement, 'DELETE') !== 0
            && strpos($statement, 'INSERT') !== 0
        ) {
            if ($records !== false && $this->entity) {
                if ($one) {
                    return Hydrator::hydrate($records, $this->entity, $this->container);
                }
    
                $newRecords = [];
    
                foreach ($records as $record) {
                    $newRecords[] = Hydrator::hydrate($record, $this->entity, $this->container);
                }
    
                return $newRecords;
            }
        }

        return $records;
    }

    /**
     * Table setter
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Update an item on database
     * @param int $id
     * @param array $values
     * @return mixed
     */
    public function update(int $id, array $values)
    {
        $attributes[':id'] = $id;
        $preparedSets = [];

        foreach ($values as $key => $value) {
            $preparedSets[] = "$key = :$key";
            $attributes[":$key"] = $value;
        }

        $statement = (new StatementBuilder())
            ->update($this->table)
            ->set($preparedSets)
            ->where('id = :id');

        return $this->query((string)$statement, $attributes, true);
    }
}
