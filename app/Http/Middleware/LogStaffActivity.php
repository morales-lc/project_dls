<?php

namespace App\Http\Middleware;

use App\Models\StaffActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogStaffActivity
{
    private const ROUTE_SUBJECT_MAP = [
        'mides' => 'MIDES document',
        'sidlak' => 'SIDLAK journal',
        'user' => 'user account',
        'staff' => 'staff account',
        'libraries.staff' => 'library staff',
        'lira' => 'LiRA request',
        'alinet' => 'ALINET appointment',
        'alert-services' => 'alert service',
        'post' => 'post',
        'information_literacy' => 'information literacy item',
        'e-libraries' => 'online library item',
        'catalog' => 'catalog record',
        'marc' => 'MARC import',
        'admin.backup' => 'backup configuration',
    ];

    private const ACTION_MAP = [
        'store' => 'added',
        'add' => 'added',
        'create' => 'opened create form for',
        'update' => 'updated',
        'edit' => 'opened edit form for',
        'destroy' => 'deleted',
        'delete' => 'deleted',
        'remove' => 'removed',
        'toggle' => 'toggled',
        'status' => 'updated status of',
        'import' => 'imported',
        'export' => 'exported',
        'run' => 'ran',
        'manage' => 'managed',
        'index' => 'viewed',
        'show' => 'viewed',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user()) {
            return $response;
        }

        $role = (string) ($request->user()->role ?? '');
        if (!in_array($role, ['admin', 'librarian'], true)) {
            return $response;
        }

        if ($request->isMethod('OPTIONS')) {
            return $response;
        }

        $routeName = optional($request->route())->getName();
        if (is_string($routeName) && str_starts_with($routeName, 'debugbar.')) {
            return $response;
        }

        $query = $request->query();
        $queryString = empty($query) ? null : substr(json_encode($query), 0, 1000);
        $routeParams = optional($request->route())->parameters() ?? [];

        [$action, $subjectType, $description] = $this->buildDescription(
            $request->method(),
            $routeName,
            $routeParams
        );
        $subjectId = $this->extractSubjectId($routeParams);

        StaffActivityLog::create([
            'user_id' => $request->user()->id,
            'role' => $role,
            'method' => $request->method(),
            'path' => $request->path(),
            'route_name' => $routeName,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'meta' => [
                'query' => $queryString,
            ],
        ]);

        return $response;
    }

    private function buildDescription(string $method, ?string $routeName, array $routeParams): array
    {
        $routeName = $routeName ?? '';
        $segments = $routeName === '' ? [] : explode('.', $routeName);

        $subject = 'resource';
        foreach (self::ROUTE_SUBJECT_MAP as $key => $label) {
            if (str_starts_with($routeName, $key) || in_array($key, $segments, true)) {
                $subject = $label;
                break;
            }
        }

        $action = $this->guessAction($method, $segments);
        $description = ucfirst($action) . ' ' . $subject;

        $subjectId = $this->extractSubjectId($routeParams);
        if ($subjectId !== null) {
            $description .= ' #' . $subjectId;
        }

        return [$action, $subject, $description];
    }

    private function guessAction(string $method, array $segments): string
    {
        foreach (array_reverse($segments) as $segment) {
            if (isset(self::ACTION_MAP[$segment])) {
                return self::ACTION_MAP[$segment];
            }
        }

        return match ($method) {
            'POST' => 'added',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'viewed',
        };
    }

    private function extractSubjectId(array $params): ?int
    {
        foreach (['id', 'user', 'post', 'journal', 'document'] as $key) {
            if (!array_key_exists($key, $params)) {
                continue;
            }

            $value = $params[$key];
            if (is_object($value) && method_exists($value, 'getKey')) {
                $value = $value->getKey();
            }

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}
