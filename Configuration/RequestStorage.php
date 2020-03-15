<?php

namespace LSBProject\RequestBundle\Configuration;

use Exception;
use LSBProject\RequestBundle\Exception\ConfigurationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 */
class RequestStorage extends ConfigurationAnnotation
{
    const BODY  = 'body';
    const QUERY = 'query';
    const ATTR  = 'attributes';

    const TYPES = [
        self::BODY,
        self::QUERY,
        self::ATTR,
    ];

    /**
     * @var string[]
     */
    private $sources = [];

    /**
     * @param $value
     * @throws Exception
     */
    public function setValue($value)
    {
        $this->setSource($value);
    }

    /**
     * @param string[] $sources
     * @throws Exception
     */
    public function setSource($sources)
    {
        foreach ($sources as $source) {
            if (!in_array($source, self::TYPES)) {
                throw new ConfigurationException(
                    'Unknown storage type. Available types: ' . implode(',', self::TYPES)
                );
            }
        }

        $this->sources = $sources;
    }

    /**
     * @return string[]
     */
    public function getSource()
    {
        return $this->sources;
    }

    /**
     * {@inheritDoc}
     */
    public function getAliasName()
    {
        return 'converter';
    }

    /**
     * {@inheritDoc}
     */
    public function allowArray()
    {
        return true;
    }
}
