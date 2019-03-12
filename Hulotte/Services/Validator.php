<?php

namespace Hulotte\Services;

use \DateTime;
use Hulotte\{
    Services\Dictionary,
    Services\Validator\ValidationError
};

/**
 * Class Validator
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Validator
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var ValidationError[]
     */
    private $errors = [];

    /**
     * @var array
     */
    private $params;

    /**
     * Validator constructor
     * @param array $params
     */
    public function __construct(Dictionary $dictionary, array $params)
    {
        $this->dictionary = $dictionary;
        $this->params = $params;
    }

    /**
     * Verify if a dateTime is valid
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();

        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'dateTime', [$format]);
        }

        return $this;
    }
    
    /**
     * Verify if a email is valid
     * @param string $key
     * @return Validator
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        
        return $this;
    }

    /**
     * Verify if an entry exists
     * @example exists->('name', $userTable)
     * @example exists->(['role_id', 'role'], $userTable)
     * @param string|array $key
     * @param mixed $table
     * @return Validator
     */
    public function exists($key, $table): Validator
    {
        if (is_array($key)) {
            $value = $this->getValue($key[1]);
            $errorKey = $key[1];
            $key = $key[0];
        } else {
            $value = $this->getValue($key);
            $errorKey = $key;
        }

        $record = $table->isExists($key, $value);
  
        if ($record === false) {
            $this->addError($errorKey, 'exists', [$table->getTable()]);
        }

        return $this;
    }

    /**
     * Errors getter
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Test if errors exists
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Verify if field have the right length
     * @param string $key
     * @param null|int $min
     * @param null|int $max
     * @return Validator
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);

        if ($min !== null
            && $max !== null
            && ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);

            return $this;
        }

        if ($min !== null && $length < $min) {
            $this->addError($key, 'minLength', [$min]);

            return $this;
        }

        if ($max !== null && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }

    /**
     * Verify if fields are empty
     * @param string[] ...$keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);

            if ($value === null || empty($value)) {
                $this->addError($key, 'empty');
            }
        }

        return $this;
    }

    /**
     * Verify if an entry not exists
     * @param string $key
     * @param mixed $table
     * @return Validator
     */
    public function notExists(string $key, $table): Validator
    {
        $value = $this->getValue($key);
        $record = $table->isExists($key, $value);

        if ($record !== false) {
            $this->addError($key, 'notExists', [$table->getTable()]);
        }

        return $this;
    }

    /**
     * Verify if all keys are in params
     * @param string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);

            if ($value === null || $value === '') {
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    /**
     * Verify if slug is valid
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';

        if ($value !== null && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }

        return $this;
    }

    /**
     * Add an error
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($this->dictionary, $key, $rule, $attributes);
    }

    /**
     * Get a value from params
     * @var string $key
     * @return mixed
     */
    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }
}
