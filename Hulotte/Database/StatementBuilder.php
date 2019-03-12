<?php

namespace Hulotte\Database;

/**
 * Class StatementBuilder
 *
 * @package Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class StatementBuilder
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $delete;

    /**
     * @var array
     */
    private $from;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $insert;

    /**
     * @var array
     */
    private $joins = [];

    /**
     * @var string
     */
    private $limit;

    /**
     * @var array
     */
    private $order = [];

    /**
     * @var array
     */
    private $select;

    /**
     * @var array
     */
    private $sets;

    /**
     * @var string
     */
    private $update;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $where = [];

    /**
     * Get the statement
     * @return string
     */
    public function __tostring(): string
    {
        if ($this->insert) {
            $parts = $this->buildInsertStatement();
        } elseif ($this->delete) {
            $parts = $this->buildDeleteStatement();
        } elseif ($this->update) {
            $parts = $this->buildUpdateStatement();
        } else {
            $parts = $this->buildSelectStatement();
        }

        return join(' ', $parts);
    }

    /**
     * Define the fields to select for an insert
     * @example ->columns('id')
     * @example ->columns(['id', 'label'])
     * @param string|string[] $fields
     * @return StatementBuilder
     */
    public function columns($fields): self
    {
        if (is_string($fields)) {
            $this->columns[] = $fields;
        } else {
            $this->columns = $fields;
        }

        return $this;
    }

    /**
     * Define the table to delete
     * @param string $table
     * @return StatementBuilder
     */
    public function delete(string $table): self
    {
        $this->delete = $table;

        return $this;
    }

    /**
     * Define the table for SELECT statement
     * @example ->from('post', 'p')
     * @param string $table
     * @param null|string $alias
     * @return StatementBuilder
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }

        return $this;
    }

    /**
     * Define the group condition
     * @example ->group('label')
     * @param string $field
     * @return StatementBuilder
     */
    public function group(string $field): self
    {
        $this->group = $field;

        return $this;
    }

    /**
     * Define the table to insert
     * @param string $table
     * @return StatementBuilder
     */
    public function insert(string $table): self
    {
        $this->insert = $table;

        return $this;
    }

    /**
     * Define the joins
     * @example ->join('category as c', 'c.id = p.category_id')
     * @example ->join('category as c', 'c.id = p.category_id', 'inner')
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return StatementBuilder
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];

        return $this;
    }

    /**
     * Define the limit condition
     * @example ->limit(10)
     * @example ->limit(10, 14)
     * @param int $start
     * @param null|int $end
     * @return StatementBuilder
     */
    public function limit(int $start, ?int $end = null): self
    {
        $this->limit = "$start";

        if (!is_null($end)) {
            $this->limit .= ', ' . $end;
        }

        return $this;
    }

    /**
     * Define the order conditions
     * @example ->order('createdAt DESC')
     * @example ->order(['createdAt DESC', 'label ASC'])
     * @param string|string[] $conditions
     * @return StatementBuilder
     */
    public function order($conditions): self
    {
        if (is_string($conditions)) {
            $this->order[] = $conditions;
        } else {
            $this->order = $conditions;
        }

        return $this;
    }

    /**
     * Define the fields to select
     * @example ->select('id')
     * @example ->select(['id', 'label'])
     * @param string|string[] $fields
     * @return StatementBuilder
     */
    public function select($fields): self
    {
        if (is_string($fields)) {
            $this->select[] = $fields;
        } else {
            $this->select = $fields;
        }

        return $this;
    }

    /**
     * Define the fields to set for an update
     * @example ->set('id = :id')
     * @example ->set(['id = :id', 'label = :label'])
     * @param string|string[] $fields
     * @return StatementBuilder
     */
    public function set($fields): self
    {
        if (is_string($fields)) {
            $this->sets[] = $fields;
        } else {
            $this->sets = $fields;
        }

        return $this;
    }

    /**
     * Define the table to update
     * @param string $table
     * @return StatementBuilder
     */
    public function update(string $table): self
    {
        $this->update = $table;

        return $this;
    }

    /**
     * Define the values for insert
     * @example ->values('My title', 'my-slug')
     * @example ->values('My first title', 'my-first-title')->values('My next title', 'my-next-title')
     * @param string|string[] $values
     * @return StatementBuilder
     */
    public function values($values): self
    {
        if (is_string($values)) {
            $this->values[] = [$values];
        } else {
            $this->values[] = $values;
        }

        return $this;
    }

    /**
     * Define the where conditions
     * @example ->where('a = :a OR b = :b')
     * @example ->where(['a = :a OR b = :b', 'c = :c'])
     * @param string|string[] $conditions
     * @return StatementBuilder
     */
    public function where($conditions): self
    {
        if (is_string($conditions)) {
            $this->where[] = $conditions;
        } else {
            $this->where = $conditions;
        }

        return $this;
    }

    /**
     * Construct a DELETE statement
     * @return array
     */
    private function buildDeleteStatement(): array
    {
        $parts[] = 'DELETE FROM ' . $this->delete;

        if (!empty($this->where)) {
            $parts[] = $this->buildWhere();
        }

        return $parts;
    }

    /**
     * Contruct the from declaration
     * @return string
     */
    private function buildFrom(): string
    {
        $from = [];

        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$value as $key";
            } else {
                $from[] = $value;
            }
        }

        return join(', ', $from);
    }

    /**
     * Construct an INSERT statement
     * @return array
     */
    private function buildInsertStatement(): array
    {
        $parts[] = 'INSERT INTO ' . $this->insert;
        $parts[] = '(' . join(', ', $this->columns) . ')';
        $parts[] = 'VALUES';
        $parts[] = join(', ', $this->buildValues());

        return $parts;
    }

    /**
     * Construct a SELECT statement
     * @return array
     */
    private function buildSelectStatement(): array
    {
        $parts[] = 'SELECT';

        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }

        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();

        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }

        if (!empty($this->where)) {
            $parts[] = $this->buildWhere();
        }

        if ($this->group) {
            $parts[] = 'GROUP BY ' . $this->group;
        }

        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }

        if ($this->limit) {
            $parts[] = 'LIMIT';
            $parts[] = $this->limit;
        }

        return $parts;
    }

    /**
     * Construct an UPDATE statement
     * @return array
     */
    private function buildUpdateStatement(): array
    {
        $parts[] = 'UPDATE ' . $this->update;
        $parts[] = 'SET';
        $parts[] = join(', ', $this->sets);
        $parts[] = $this->buildWhere();

        return $parts;
    }

    /**
     * Construct the VALUES condition for insert statement
     * @return array
     */
    private function buildValues(): array
    {
        $array = [];

        foreach ($this->values as $list) {
            $line = '(';
            $values = [];

            foreach ($list as $value) {
                if (is_string($value)
                    && strpos($value, '?') === false
                    && strpos($value, ':') === false
                ) {
                    $values[] = '"' . $value . '"';
                } else {
                    $values[] = $value;
                }
            }

            $line .= join(', ', $values);
            $line .= ')';

            $array[] = $line;
        }

        return $array;
    }

    /**
     * Construct the where condition
     * @return string
     */
    private function buildWhere(): string
    {
        return 'WHERE (' . join(') AND (', $this->where) . ')';
    }
}
