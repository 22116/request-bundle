<?php

namespace LSBProject\RequestBundle\Request\Factory\Param;

use LSBProject\RequestBundle\Configuration\PropConfigurationInterface;
use LSBProject\RequestBundle\Request\Factory\RequestFactoryInterface;
use LSBProject\RequestBundle\Request\Manager\RequestManagerInterface;
use LSBProject\RequestBundle\Util\ReflectionExtractor\DTO\Extraction;
use Symfony\Component\HttpFoundation\Request;
use LSBProject\RequestBundle\Request\AbstractRequest;

final class DtoParamFactory implements ParamAwareFactoryInterface
{
    use RequestCopyTrait;

    /**
     * @var RequestManagerInterface
     */
    private $requestManager;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * ConverterParamFactory constructor.
     *
     * @param RequestManagerInterface $requestManager
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(RequestManagerInterface $requestManager, RequestFactoryInterface $requestFactory)
    {
        $this->requestManager = $requestManager;
        $this->requestFactory = $requestFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(PropConfigurationInterface $configuration)
    {
        return $configuration->isDto()
            && $configuration->getType()
            && !$configuration->isBuiltInType()
            && !$configuration->isCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function create(Extraction $data, Request $request, PropConfigurationInterface $configuration)
    {
        $params = $this->requestManager->get($data, $request);
        $params = is_array($params) ? $params : [];

        /** @var class-string<AbstractRequest> $type */
        $type = $configuration->getType();

        return $this->requestFactory->create(
            $type,
            $this->cloneRequest($request, $params, $data->getRequestStorage())
        );
    }
}
