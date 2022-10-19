<?php

declare(strict_types=1);

namespace App\Foundation\Http;

use App\Foundation\Utils\Json;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Base request class.
 */
class Request
{
    protected readonly RequestStack $requestStack;

    protected readonly Json $json;

    public function __construct(RequestStack $requestStack, Json $json)
    {
        $this->requestStack = $requestStack;
        $this->json = $json;
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
            return $this->json->jsonToArray($this->request()->getContent());
        }

        return $this->request()->request->all();
    }

    /**
     * Checks if request is JSON.
     */
    public function isJson(): bool
    {
        return $this->request()->getContentType() === 'json';
    }

    /**
     * Returns data required for form submission.
     */
    public function formInput(): string|array|null
    {
        return $this->all();
    }
}
