<?php

namespace LSBProject\RequestBundle\Request\Factory\Param;

use LSBProject\RequestBundle\Configuration\RequestStorage;
use Symfony\Component\HttpFoundation\Request;

trait RequestCopyTrait
{
    /**
     * @param Request              $request
     * @param array<string, mixed> $params
     * @param RequestStorage|null  $storage
     *
     * @return Request
     */
    private function cloneRequest(Request $request, array $params, RequestStorage $storage = null)
    {
        $request = clone $request;
        $storage = $storage ?: new RequestStorage(RequestStorage::TYPES);

        if (in_array(RequestStorage::QUERY, $storage->getSources())) {
            $request->query->replace($params);
        }

        if (in_array(RequestStorage::BODY, $storage->getSources())) {
            $request->request->replace($params);
        }

        if (in_array(RequestStorage::HEAD, $storage->getSources())) {
            $request->headers->replace($params);
        }

        if (in_array(RequestStorage::COOKIE, $storage->getSources())) {
            $request->cookies->replace($params);
        }

        return $request;
    }
}
