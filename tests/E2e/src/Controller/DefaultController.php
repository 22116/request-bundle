<?php declare(strict_types=1);

namespace App\Controller;

use App\Request\TestAttributesRequest;
use App\Request\TestBodyRequest;
use App\Request\TestQueryRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractFOSRestController
{
    /**
     * @Route("/query", methods={"GET"})
     */
    public function testQueryRequest(TestQueryRequest $request): array
    {
        return [
            'foo' => $request->foo,
            'barBaz' => $request->barBaz,
            'dto' => $request->dto->getFoo()
        ];
    }

    /**
     * @Route("/body", methods={"POST"})
     */
    public function testBodyRequest(TestBodyRequest $request): array
    {
        return [
            'foo' => $request->foo,
            'barBaz' => $request->barBaz,
            'dto' => $request->dto->getFoo()
        ];
    }

    /**
     * @Route("/attributes/{foo_attr}")
     */
    public function testAttributesRequest(TestAttributesRequest $request): array
    {
        return [
            'foo' => $request->fooAttr,
            'bar' => $request->bar,
            'entityA' => $request->entityA->getText(),
            'entityB' => $request->entityB->getText(),
            'entityC' => $request->entityC->getText()
        ];
    }
}