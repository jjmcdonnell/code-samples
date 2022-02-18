<?php

namespace App\Http\Controllers\Portal\Categories\Plates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meta\Meta;
use App\Models\Products\Plates\Plate;

class PlatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('content.private.pages.products.plates.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = false)
    {
        $Reference = false;
        if($id){
            $Reference = Plate::getSinglePlate($id); // hybrid DB Query ORM model
        }
        $meta = Meta::getAllFields(); // pure ORM eloquent model
        return view('content.private.pages.products.plates.create', compact('meta', 'Reference'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        return view('content.private.pages.products.plates.show');
    }

}
