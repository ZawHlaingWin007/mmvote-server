<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
         $host = $request->getHost();

        // 🚫 Block all *.mtshmyanmar.com
        //if (preg_match('/\.mtshmyanmar\.com$/', $host)) {
        //    abort(403, 'Access denied for this domain.');
        //}
	if (preg_match('/\.mcb\.com.mm$/', $host)) {
            abort(403, 'Access denied for this domain.');
        }


        // ✅ Allow other domains as normal
        return parent::handle($request, $next);
    }
}
