<?php declare(strict_types=1);

namespace App\Controller;

use App\Request\DTO\TestParamsA;
use App\Request\TestAttributesRequest;
use App\Request\TestBodyRequest;
use App\Request\TestDiscriminatedRequest;
use App\Request\TestJsonRpcRequest;
use App\Request\TestQueryRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LSBProject\RequestBundle\Configuration as LSB;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Route("/query", methods={"GET"})
     */
    public function testQueryRequest(TestQueryRequest $request): array
    {
        return [
            'foo' => $request->getFoo(),
            'barBaz' => $request->getBarBaz(),
            'dto' => $request->dto->getFoo(),
            'fooEnum' => $request->fooEnum->value,
        ];
    }

    /**
     * @Rest\View()
     * @Route("/body", methods={"POST"})
     */
    public function testBodyRequest(TestBodyRequest $request): array
    {
        return [
            'foo' => $request->foo,
            'barBaz' => $request->barBaz,
            'dto' => $request->dto->getFoo(),
        ];
    }

    /**
     * @Rest\View()
     * @Route("/attributes/{foo_attr}")
     */
    public function testAttributesRequest(TestAttributesRequest $request): array
    {
        return [
            'foo' => $request->fooAttr,
            'bar' => $request->bar,
            'entityA' => $request->entityA->getText(),
            'entityB' => $request->entityB->getText(),
            'entityC' => $request->entityC->getText(),
            'entityD' => $request->entityD->getText(),
        ];
    }

    /**
     * @Rest\View()
     * @Route("/jsonrpc")
     */
    public function jsonrpcRequest(TestJsonRpcRequest $request): array
    {
        return [
            'jsonrpc' => $request->jsonrpc,
            'method' => $request->methodName,
            'id' => $request->id,
            'params' => [
                'foo' => $request->params->foo,
                'bar' => [
                    ['foo' => $request->params->bar[0]->foo],
                    ['foo' => $request->params->bar[1]->foo],
                ],
                'baz' => [
                    ['text' => $request->params->baz[0]->getText()],
                ],
            ],
        ];
    }

    /**
     * @Rest\View()
     * @Route("/head")
     * @LSB\Request("params", storage=@LSB\RequestStorage({LSB\RequestStorage::HEAD}))
     */
    public function testHeadRequest(TestParamsA $params): array
    {
        return [
            'foo' => $params->foo,
        ];
    }

    /**
     * @Rest\View()
     * @Route("/discriminated/foo")
     * @LSB\Request("params")
     */
    public function testDiscriminatedFooRequest(TestDiscriminatedRequest $params): array
    {
        return [
            'foo' => $params->discriminated->type,
        ];
    }

    /**
     * @Rest\View()
     * @Route("/discriminated")
     */
    public function testDiscriminatedRequest(TestDiscriminatedRequest $params): array
    {
        return (array) $params;
    }
}
