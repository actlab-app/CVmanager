<?php

namespace App\Http\Middleware;

use App\Models\ReferenceToken;
use App\Models\ReferenceVisit;
use App\Models\SiteSetting;
use App\Support\ReferenceUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferenceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->query(ReferenceUrl::PARAMETER);
        $hasToken = is_string($rawToken) && trim($rawToken) !== '';
        $settings = SiteSetting::query()->first();
        $blockWithoutToken = (bool) $settings?->block_visitors_without_reference_token;

        if (! $hasToken) {
            if ($blockWithoutToken) {
                return redirect()->route('reference-token.required');
            }

            return $next($request);
        }

        $token = ReferenceToken::query()
            ->active()
            ->where('token', ReferenceUrl::normalizeToken((string) $rawToken))
            ->first();

        if (! $token) {
            session()->forget(ReferenceUrl::SESSION_KEY);

            if ($blockWithoutToken) {
                return redirect()->route('reference-token.required');
            }

            return $next($request);
        }

        session()->put(ReferenceUrl::SESSION_KEY, [
            'id' => $token->id,
            'token' => $token->token,
            'name' => $token->name,
            'image' => $token->image,
        ]);

        if ($this->shouldRecordVisit($request)) {
            $this->recordVisit($request, $token);
        }

        return $next($request);
    }

    private function shouldRecordVisit(Request $request): bool
    {
        if ($request->headers->has('X-Livewire-Navigate')) {
            return false;
        }

        return $request->routeIs('about', 'cv', 'contact', 'portfolio.*');
    }

    private function recordVisit(Request $request, ReferenceToken $token): void
    {
        $now = now();

        $token->increment('visits_count');
        $token->forceFill(['last_visited_at' => $now])->save();

        ReferenceVisit::query()->create([
            'reference_token_id' => $token->id,
            'path' => $request->path(),
            'landing_url' => $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'ip_hash' => $this->hashValue($request->ip()),
            'user_agent_hash' => $this->hashValue($request->userAgent()),
            'visited_at' => $now,
        ]);
    }

    private function hashValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return hash_hmac('sha256', $value, (string) config('app.key'));
    }
}
