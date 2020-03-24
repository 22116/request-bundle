<?php

namespace LSBProject\RequestBundle\Request\Factory;

use LSBProject\RequestBundle\Request\AbstractRequest;
use LSBProject\RequestBundle\Request\Manager\RequestManagerInterface;
use LSBProject\RequestBundle\Util\ReflectionExtractor\ReflectionExtractorInterface;
use ReflectionClass;
use ReflectionProperty;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * @var ReflectionExtractorInterface
     */
    private $reflectionExtractor;

    /**
     * @var RequestManagerInterface
     */
    private $requestManager;

    /**
     * @param ReflectionExtractorInterface $reflectionExtractor
     * @param RequestManagerInterface      $requestManager
     */
    public function __construct(
        ReflectionExtractorInterface $reflectionExtractor,
        RequestManagerInterface $requestManager
    ) {
        $this->reflectionExtractor = $reflectionExtractor;
        $this->requestManager = $requestManager;
    }

    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function create($class)
    {
        $meta = new ReflectionClass($class);
        $props = $this->reflectionExtractor->extract($meta, $this->filterProps($meta));

        /** @var AbstractRequest $object */
        $object = $meta->newInstance();

        foreach ($props as $prop) {
            $var = $prop->getConfiguration()->isBuiltInType() ?
                $this->requestManager->get($prop) :
                $this->requestManager->getFromParamConverters($prop);

            if ($meta->hasMethod($method = 'set' . ucfirst($prop->getName()))) {
                $object->$method($var);
            } else {
                $object->{$prop->getName()} = $var;
            }
        }

        return $object;
    }


    /**
     * @param ReflectionClass<AbstractRequest> $meta
     *
     * @return string[]
     */
    private function filterProps(ReflectionClass $meta)
    {
        $props = array_filter(
            $meta->getProperties(),
            function (ReflectionProperty $prop) use ($meta) {
                $method = 'set' . ucfirst($prop);

                return $prop->getDeclaringClass()->getName() === $meta->getName() &&
                    ($prop->isPublic() || ($meta->hasMethod($method) && $meta->getMethod($method)->isPublic()));
            }
        );

        return array_map(function (ReflectionProperty $property) {
            return $property->getName();
        }, $props);
    }
}
