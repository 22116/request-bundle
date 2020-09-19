<?php

namespace LSBProject\RequestBundle\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

abstract class AbstractPropConfiguration extends ConfigurationAnnotation implements PropConfigurationInterface
{
    const ALIAS = 'lsbproject_converter';
    const BUILTIN_TYPES = [null, "string", "int", "float", "bool", "array"];

    /**
     * {@inheritDoc}
     */
    public function isBuiltInType()
    {
        return in_array($this->getType(), self::BUILTIN_TYPES);
    }

    /**
     * {@inheritDoc}
     */
    public function getAliasName()
    {
        return self::ALIAS;
    }

    /**
     * {@inheritDoc}
     */
    public function allowArray()
    {
        return false;
    }
}
