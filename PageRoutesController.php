<?php

namespace App\Http\Controllers\Platform;

use Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\RouteMetadata;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class PageRoutesController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $routes = \App\Models\RouteMetadata::all();
        $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        return view('private.pages.content.route-metadata.list',
                [
                    'page_info' => $RouteMetadata,
                    'routes' => $routes,
                    'routes_count' => count($routes),
                    'settings' => $this->getUserSettings()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        return view('private.pages.content.route-metadata.create',
                [
                    'page_info' => $RouteMetadata,
                    'settings' => $this->getUserSettings()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $error = false;
        if (isset($request['create_complete_route']) && $request['create_complete_route'] == "on") {
            if ($this->create_complete_route($request->all())) {
                $error = false;
            } else {
                $error = true;
            }
        }
        if ($error == false) {
            if ($this->validator($request->all())->validate()) {
                $success = RouteMetadata::create([
                            'scope' => $request['scope'],
                            'title' => $request['title'],
                            'route' => $request['route'],
                            'keywords' => $request['keywords'],
                            'subtitle' => $request['subtitle'],
                            'author' => $request['author'],
                            'description' => $request['description'],
                            'generator' => $request['generator'],
                            'blurb' => $request['blurb'],
                            'twitter_site' => $request['twitter_site'],
                            'twitter_creator' => $request['twitter_creator'],
                            'twitter_card' => $request['twitter_card'],
                            'og_title' => $request['og_title'],
                            'og_image' => $request['og_image'],
                            'og_url' => $request['og_url'],
                            'og_type' => $request['og_type'],
                            'og_audio' => $request['og_audio'],
                            'og_description' => $request['og_description'],
                            'og_determiner' => $request['og_determiner'],
                            'og_locale' => $request['og_locale'],
                            'og_site_name' => $request['og_site_name'],
                            'og_video' => $request['og_video'],
                ]);
                if ($success) {
                    $log_message = "New Route Created: " . request()->ip() . " ID =  " . $success;
                    Log::channel('slack')->info($log_message);
                    $renderlogic_admin = \App\User::find(1);
                    $renderlogic_admin->notify(new \App\Notifications\NewRouteNotification($log_message));
                    $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
                    return view('private.pages.content.route-metadata.create',
                            [
                                'page_info' => $RouteMetadata,
                                'success' => $success,
                                'settings' => $this->getUserSettings()
                    ]);
                }
            }
        } else {
            abort(500);
       }
    }

    protected function validator(array $data) {
        $constraints = [
            'scope' => ['required', 'in:Website,Platform,EDT'],
            'title' => ['required', 'string', 'max:255'],
            'keywords' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'generator' => ['required', 'string', 'max:255'],
            'blurb' => ['required', 'string', 'max:4096'],
            'twitter_site' => ['nullable', 'string', 'max:255'],
            'twitter_creator' => ['nullable', 'string', 'max:255'],
            'twitter_card' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_image' => ['nullable', 'url'],
            'og_url' => ['nullable', 'url'],
            'og_type' => ['nullable', 'string', 'max:255'],
            'og_audio' => ['nullable', 'url'],
            'og_description' => ['nullable', 'string', 'max:255'],
            'og_determiner' => ['nullable', 'string', 'max:255'],
            'og_locale' => ['nullable', 'string', 'max:255'],
            'og_site_name' => ['nullable', 'string', 'max:255'],
            'og_video' => ['nullable', 'url']
        ];
        if (isset($data['id'])) {
            $constraints['route'] = ['required', 'string', 'max:255', Rule::unique('route_metadata', 'route')->ignore($data['id'])];
        } else {
            $constraints['route'] = ['required', 'string', 'max:255', 'unique:route_metadata'];
        }
        return Validator::make($data, $constraints);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $route = \App\Models\RouteMetadata::where('id', '=', $id)->firstOrFail();
        $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        return view('private.pages.content.route-metadata.show',
                [
                    'page_info' => $RouteMetadata,
                    'route' => $route,
                    'settings' => $this->getUserSettings()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $route = \App\Models\RouteMetadata::where('id', '=', $id)->firstOrFail();
        $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        return view('private.pages.content.route-metadata.edit',
                [
                    'page_info' => $RouteMetadata,
                    'route' => $route,
                    'settings' => $this->getUserSettings()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * 
     * @todo add ignore to validation rule for updating
     */
    public function update(Request $request, $id) {
        $route = \App\Models\RouteMetadata::find($id);
        if ($this->validator($request->all())->validate()) {
            $route->scope = $request['scope'];
            $route->title = $request['title'];
            $route->route = $request['route'];
            $route->keywords = $request['keywords'];
            $route->subtitle = $request['subtitle'];
            $route->author = $request['author'];
            $route->description = $request['description'];
            $route->generator = $request['generator'];
            $route->blurb = $request['blurb'];
            $route->twitter_site = $request['twitter_site'];
            $route->twitter_creator = $request['twitter_creator'];
            $route->twitter_card = $request['twitter_card'];
            $route->og_title = $request['og_title'];
            $route->og_image = $request['og_image'];
            $route->og_url = $request['og_url'];
            $route->og_type = $request['og_type'];
            $route->og_audio = $request['og_audio'];
            $route->og_description = $request['og_description'];
            $route->og_determiner = $request['og_determiner'];
            $route->og_locale = $request['og_locale'];
            $route->og_site_name = $request['og_site_name'];
            $route->og_video = $request['og_video'];
        }
        $result = $route->save();
        if ($result) {
            Log::channel('slack')->info("Route Updated!: " . request()->ip() . " ID =  " . $id);
        } else {
            Log::channel('slack')->info("Route Update Failed: " . request()->ip() . " ID =  " . $id);
        }
        $RouteMetadata = \App\Models\RouteMetadata::where('route', '=', Route::getFacadeRoot()->current()->uri())->firstOrFail();
        return view('private.pages.content.route-metadata.edit',
                [
                    'page_info' => $RouteMetadata,
                    'route' => $route,
                    'result' => $result,
                    'settings' => $this->getUserSettings()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // see api routes 
    }

    private function create_complete_route($request) {
        $result = Validator::make($request, [
                    'route_controller' => ['required', 'alpha', 'max:50'],
                    'route_method' => ['required', 'alpha', 'max:50'],
                    'route_http_method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE'],
        ]);
        if ($result->validate()) {
            if ($this->write_route_entry($request)) {
                $result = true;
            } else {
                $result = false;
            }
            if (isset($request['generate_new_controller']) && $request['generate_new_controller'] == "on") {
                if ($this->generate_route_controller($request)) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        } else {
            return false;
        }
        return $result;
    }

    private function write_route_entry($request) {
        $file = null;
        switch ($request['scope']) {
            case 'Website': {
                    $file = "public.addendum.php";
                }break;
            case 'Platform': {
                    $file = "authenticated.addendum.php";
                }break;
        }
        $string = "Route::"
                . strtolower($request['route_http_method'])
                . "('" . $request['route'] . "', '"
                . $request['scope']
                . "\\" . $request['route_controller']
                . "@" . $request['route_method']
                . "');";
        if (Storage::disk('routes')->append($file, $string)) {
            Log::channel('slack')->info("Route Entry Written!: " . $string);
            return true;
        } else {
            return false;
        }
    }

    private function generate_route_controller($request) {
        $command = "make:controller " . $request['scope'] . "\\\\" . $request['route_controller'] . " --resource";
        $result = Artisan::call($command);
        if ($result == 0) {
            return true;
        } else {
            return false;
        }
    }

}
