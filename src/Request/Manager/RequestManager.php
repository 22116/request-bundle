<?php

namespace LSBProject\RequestBundle\Request\Manager;

use LSBProject\RequestBundle\Configuration\Entity;
use LSBProject\RequestBundle\Configuration\PropConfigurationInterface;
use LSBProject\RequestBundle\Configuration\RequestStorage;
use LSBProject\RequestBundle\Exception\ConfigurationException;
use LSBProject\RequestBundle\Util\Factory\ParamConverterFactoryInterface;
use LSBProject\RequestBundle\Util\NamingConversion\NamingConversionInterface;
use LSBProject\RequestBundle\Util\ReflectionExtractor\DTO\Extraction;
use LSBProject\RequestBundle\Util\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

final class RequestManager implements RequestManagerInterface
{
    /**
     * @var NamingConversionInterface
     */
    private $namingConversion;

    /**
     * @var ParamConverterFactoryInterface
     */
    private $paramConverterFactory;

    /**
     * @var ParamConverterManager
     */
    private $converterManager;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @param NamingConversionInterface      $namingConversion
     * @param ParamConverterFactoryInterface $paramConverterFactory
     * @param ParamConverterManager          $converterManager
     * @param StorageInterface               $storage
     * @param ContainerInterface             $container
     */
    public function __construct(
        NamingConversionInterface $namingConversion,
        ParamConverterFactoryInterface $paramConverterFactory,
        ParamConverterManager $converterManager,
        StorageInterface $storage,
        ContainerInterface $container
    ) {
        $this->namingConversion = $namingConversion;
        $this->paramConverterFactory = $paramConverterFactory;
        $this->converterManager = $converterManager;
        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function get(Extraction $param, Request $request)
    {
        return $this->storage->get(
            $this->getParameterName($param->getConfiguration(), $param->getRequestStorage(), $param->getName()),
            $param->getRequestStorage(),
            $request
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFromParamConverters(Extraction $param, Request $request)
    {
        $config  = $param->getConfiguration();

        $params = $this->addId($config, $request, $param->getRequestStorage());
        $params = array_merge($params, $this->addOptionMapping($config, $request, $param->getRequestStorage()));

        if ($config instanceof Entity && $config->getMapping()) {
            $params = array_merge($params, $this->addPropMapping($config, $request, $param->getRequestStorage()));
        }

        $paramConfig = $this->paramConverterFactory->create($param->getName(), $config);

        try {
            $this->converterManager->apply($request, $paramConfig);
        } catch (Throwable $exception) {
            if (!$param->getConfiguration()->isOptional()) {
                throw new ConfigurationException(
                    sprintf("Cannot convert '%s' property. %s", $param->getName(), $exception->getMessage())
                );
            }
        }

        $var = $request->attributes->get($param->getName());
        $request->attributes->remove($param->getName());

        foreach ($params as $param) {
            $request->attributes->remove($param);
        }

        return $var;
    }

    /**
     * @param PropConfigurationInterface $config
     * @param Request                    $request
     * @param RequestStorage|null        $storage
     *
     * @return array<string>
     */
    private function addId(PropConfigurationInterface $config, Request $request, $storage)
    {
        $options = $config->getOptions();
        $id = isset($options['id']) ? $options['id'] : $this->storage->get('id', $storage);

        if ($id) {
            $request->attributes->set(
                $id,
                $this->storage->get(
                    $this->getParameterName($config, $storage, $id),
                    $storage,
                    $request
                )
            );
        }

        return $id ? [$id] : [];
    }

    /**
     * @param PropConfigurationInterface $config
     * @param Request                    $request
     * @param RequestStorage|null        $storage
     *
     * @return array<string>
     */
    private function addOptionMapping(PropConfigurationInterface $config, Request $request, $storage)
    {
        $result = [];
        $options = $config->getOptions();

        /** @var array<string, string> $mapping */
        $mapping = isset($options['mapping']) ? $options['mapping'] : [];

        foreach ($mapping as $alias => $option) {
            if ("expr" === $alias) {
                continue;
            }

            $request->attributes->set(
                $alias,
                $this->storage->get(
                    $this->getParameterName($config, $storage, $alias),
                    $storage,
                    $request
                )
            );
            $result[] = $alias;
        }

        return $result;
    }

    /**
     * @param Entity              $config
     * @param Request             $request
     * @param RequestStorage|null $storage
     *
     * @return array<string>
     */
    private function addPropMapping(Entity $config, Request $request, $storage)
    {
        $result = [];

        foreach ($config->getMapping() as $alias => $option) {
            $request->attributes->set(
                $alias,
                $this->storage->get(
                    $this->getParameterName($config, $storage, $option),
                    $storage,
                    $request
                )
            );
            $result[] = $alias;
        }

        return $result;
    }

    /**
     * @param PropConfigurationInterface $config
     * @param RequestStorage|null        $requestStorage
     * @param string                     $name
     *
     * @return string
     */
    private function getParameterName(PropConfigurationInterface $config, $requestStorage, $name)
    {
        $converterName = $requestStorage ? $requestStorage->getConverter() : '';

        /** @var NamingConversionInterface $converter */
        $converter = $converterName ? $this->container->get($converterName) : $this->namingConversion;

        return $config->getName() ?: $converter->normalize($name);
    }
}
