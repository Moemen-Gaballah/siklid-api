<?php

declare(strict_types=1);

namespace App\Foundation\Http;

use App\Foundation\Action\ValidatableInterface;
use App\Foundation\Util\RequestUtil;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Base request class.
 */
class Request implements ValidatableInterface
{
    protected RequestStack $requestStack;

    protected RequestUtil $util;

    public function __construct(RequestStack $requestStack, RequestUtil $util)
    {
        $this->requestStack = $requestStack;
        $this->util = $util;
    }

    /**
     * Get current request.
     *
     * @psalm-suppress NullableReturnStatement - we know that request is not null
     * @psalm-suppress InvalidNullableReturnType - we know that request is not null
     */
    public function request(): SymfonyRequest
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * Returns all request parameters.
     */
    public function all(): array
    {
        if ($this->isJson()) {
            return $this->util->json()->jsonToArray($this->request()->getContent());
        }

        return $this->request()->request->all();
    }

    /**
     * Checks if request is JSON.
     */
    public function isJson(): bool
    {
        return 'json' === $this->request()->getContentType();
    }

    /**
     * Returns data required for form submission.
     */
    public function formInput(): string|array|null
    {
        return $this->all();
    }

    /**
     * Retrieves $_GET and $_POST variables respectively.
     */
    public function get(string $key, mixed $default = null): string|int|bool|null|float
    {
        assert(is_scalar($default) || is_null($default));

        $getVariables = $this->request()->query;
        $postVariables = $this->request()->request;

        if ($getVariables->has($key)) {
            assert(is_string($default) || is_null($default));

            return $getVariables->get($key, $default);
        }

        if ($postVariables->has($key)) {
            return $postVariables->get($key, $default);
        }

        return $default;
    }
}
