<?php

namespace LSBProject\RequestBundle\Util\ReflectionExtractor;

use LSBProject\RequestBundle\Configuration\Discriminator;
use LSBProject\RequestBundle\Configuration\PropConfigurationInterface;
use LSBProject\RequestBundle\Configuration\RequestStorage;

final class Extraction
{
    const DEFAULT_VALUE = '__lsbproject_extraction_default_none 😀 😃 😄';

    /**
     * @var string
     */
    private $name;

    /**
     * @var PropConfigurationInterface
     */
    private $configuration;

    /**
     * @var RequestStorage|null
     */
    private $requestStorage;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var Discriminator|null
     */
    private $discriminator;

    /**
     * @param string                     $name
     * @param PropConfigurationInterface $configuration
     * @param RequestStorage|null        $requestStorage
     * @param mixed                      $default
     * @param Discriminator|null         $discriminator
     */
    public function __construct(
        $name,
        PropConfigurationInterface $configuration,
        $requestStorage,
        $default = self::DEFAULT_VALUE,
        Discriminator $discriminator = null
    ) {
        $this->name = $name;
        $this->configuration = $configuration;
        $this->requestStorage = $requestStorage;
        $this->default = $default;
        $this->discriminator = $discriminator;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->configuration = clone $this->configuration;
        $this->requestStorage = $this->requestStorage ? clone $this->requestStorage : $this->requestStorage;
        $this->discriminator = $this->discriminator ? clone $this->discriminator : $this->discriminator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param PropConfigurationInterface $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return PropConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return RequestStorage|null
     */
    public function getRequestStorage()
    {
        return $this->requestStorage;
    }

    /**
     * @param RequestStorage|null $requestStorage
     *
     * @return self
     */
    public function setRequestStorage($requestStorage)
    {
        $this->requestStorage = $requestStorage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default !== self::DEFAULT_VALUE;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     *
     * @return self
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return Discriminator|null
     */
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    /**
     * @param Discriminator|null $discriminator
     */
    public function setDiscriminator($discriminator)
    {
        $this->discriminator = $discriminator;
    }
}
