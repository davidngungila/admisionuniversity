<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $route        = $request->route();
        $userId       = $request->user()?->id;
        $modelType    = null;
        $modelId      = null;
        $before       = null;

        foreach ($route?->parameters() ?? [] as $param) {
            if ($param instanceof Model) {
                $modelType = get_class($param);
                $modelId   = $param->getKey();
                $before    = $param->fresh()?->toArray();
                break;
            }
        }

        $response = $next($request);

        try {
            $this->afterRequest($request, $response, $userId, $modelType, $modelId, $before);
        } catch (\Throwable $e) {
            // Audit logging must never break a request.
        }

        return $response;
    }

    protected function afterRequest(
        Request $request,
        Response $response,
        ?int $userId,
        ?string $modelType,
        $modelId,
        ?array $before,
    ): void {
        if ($this->shouldSkip($request)) {
            return;
        }

        // Fall back to the (now resolved) user for login/register requests.
        $userId = $userId ?? $request->user()?->id;

        $action = $this->describeAction($request, $modelType, $modelId);

        $after = null;
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $payload = $request->except(['_token', '_method', 'password', 'password_confirmation', 'current_password', 'old_password', 'new_password']);
            if (! empty($payload)) {
                $after = $this->cleanPayload($payload);
            }
        } elseif ($modelType && $modelId) {
            $after = $modelType::find($modelId)?->toArray();
        }

        AuditLog::log(
            Str::limit($action, 255),
            $userId,
            $modelType ? Str::limit($modelType, 100) : null,
            $modelId,
            $before,
            $after,
            $request->ip(),
            $request->userAgent(),
            $request->method(),
            Str::limit($request->fullUrl(), 255),
            $response->getStatusCode(),
        );
    }

    protected function describeAction(Request $request, ?string $modelType, $modelId): string
    {
        $method = strtoupper($request->method());
        $path   = $request->path();
        $name   = $request->route()?->getName() ?: '';
        $model  = $modelType ? class_basename($modelType) : null;

        // Readable labels for auth events
        if ($method === 'POST' && $path === 'login')    return \Illuminate\Support\Facades\Auth::check() ? 'New Login (Successful)' : 'Login Failed';
        if ($method === 'POST' && $path === 'logout')   return 'Logout';
        if ($method === 'POST' && $path === 'register') return 'New User Registration';

        if ($model) {
            return match ($method) {
                'POST', 'PUT', 'PATCH' => ($modelId ? 'Update ' : 'Create ').$model,
                'DELETE'               => 'Delete '.$model,
                default                => 'View '.$model,
            };
        }

        if (Str::endsWith($name, '.store'))   return 'Create '.$this->resource($name);
        if (Str::endsWith($name, '.destroy')) return 'Delete '.$this->resource($name);
        if (Str::endsWith($name, '.toggle'))  return 'Toggle '.$this->resource($name);
        if (Str::endsWith($name, '.update'))  return 'Update '.$this->resource($name);

        return $name ?: $method.' '.$path;
    }

    protected function resource(string $routeName): string
    {
        $segments = explode('.', $routeName);
        $last     = 'system';
        if (count($segments) >= 2) {
            $last = $segments[count($segments) - 2];
        }
        return Str::studly(Str::singular($last));
    }

    protected function shouldSkip(Request $request): bool
    {
        // Log every action when authenticated; for guests only record state-changing requests.
        if ($request->user()) {
            return false;
        }
        return in_array(strtoupper($request->method()), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    protected function cleanPayload(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->cleanPayload($value);
            } elseif (str_contains((string) $key, 'password') || $key === 'secret') {
                $payload[$key] = '[REDACTED]';
            }
        }
        return $payload;
    }
}