<?php

namespace LSBProject\RequestBundle\Request\ParamConverter;

use LSBProject\RequestBundle\Request\RequestInterface;
use LSBProject\RequestBundle\Request\Factory\RequestFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class RequestConverter implements ParamConverterInterface
{
    use ContentTypeHelperTrait;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnprocessableEntityHttpException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->convertRequestContextIfEmpty($request);

        /** @var class-string<RequestInterface> $class */
        $class = $configuration->getClass();

        $request->attributes->set($configuration->getName(), $this->requestFactory->create($class, $request));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return is_subclass_of($configuration->getClass(), RequestInterface::class);
    }
}
