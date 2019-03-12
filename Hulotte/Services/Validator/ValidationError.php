<?php

namespace Hulotte\Services\Validator;

use Hulotte\Services\Dictionary;

/**
 * Class ValidationError
 *
 * @package Hulotte\Services\Validator
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ValidationError
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $rule;

    /**
     * ValidationError constructor
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(Dictionary $dictionary, string $key, string $rule, array $attributes = [])
    {
        $this->dictionary = $dictionary;
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $params = array_merge(
            [$this->dictionary->translate("ValidationError:{$this->rule}"), $this->key],
            $this->attributes
        );

        return (string)call_user_func_array('sprintf', $params);
    }
}
