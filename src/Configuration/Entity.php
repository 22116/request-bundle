<?php

namespace LSBProject\RequestBundle\Configuration;

/**
 * @Annotation
 */
final class Entity extends PropConverter
{
    /**
     * @var string
     */
    private $expr;

    /**
     * @var array<string, string>
     */
    private $mapping;

    /**
     * @return string
     */
    public function getExpr()
    {
        return $this->expr;
    }

    /**
     * @param string $expr
     *
     * @return void
     */
    public function setExpr($expr)
    {
        $this->expr = $expr;
    }

    /**
     * @return array<string, string>
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array<string, string> $mapping
     *
     * @return void
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }
}
