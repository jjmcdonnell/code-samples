<?php
/*
* This is a custom middleware package developed by John McDonnell
* over time to populate database driven meta data fields on pages
* that may have custom fields or use a default set. 
**/

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class Metadata
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $RouteMetadata = null;
        }
        View::share('Metadata', $RouteMetadata);
        return $next($request);
    }
}
