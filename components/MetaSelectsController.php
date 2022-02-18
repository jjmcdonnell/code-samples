<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meta\ProductCategories;
use Illuminate\Http\Request;
use App\Models\Meta\ArtThemes;
use App\Models\Meta\Brands;
use App\Models\Meta\Categories;
use App\Models\Meta\Characters;
use App\Models\Meta\Conditions;
use App\Models\Meta\Cultures;
use App\Models\Meta\DecorStyles;
use App\Models\Meta\Eras;
use App\Models\Meta\Franchises;
use App\Models\Meta\Materials;
use App\Models\Meta\Occasions;
use App\Models\Meta\Shapes;
use App\Models\Meta\ManufacturerIdTypes;

class MetaSelectsController extends Controller
{
    public function getArtThemes(){
        return ArtThemes::select('id as value', 'art_theme as label')->get();
    }

    public function getBrands(){
        return Brands::select('id as value', 'brand as label')->get();
    }

    public function getCategories(){
        return Categories::select('id as value', 'category as label')->get();
    }

    public function getCharacters(){
        return Characters::select('id as value', 'character as label')->get();
    }

    public function getConditions(){
        return Conditions::select('id as value', 'condition as label')->get();
    }

    public function getCultures(){
        return Cultures::select('id as value', 'culture as label')->get();
    }

    public function getDecorStyles(){
        return DecorStyles::select('id as value', 'decor_style as label')->get();
    }

    public function getEras(){
        return Eras::select('id as value', 'era as label')->get();
    }

    public function getFranchises(){
        return Franchises::select('id as value', 'franchise as label')->get();
    }

    public function getMaterials(){
        return Materials::select('id as value', 'material as label')->get();
    }

    public function getOccasions(){
        return Occasions::select('id as value', 'occasion as label')->get();
    }

    public function getShapes(){
        return Shapes::select('id as value', 'shape as label')->get();
    }

    public function getManufacturerIdTypes(){
        return ManufacturerIdTypes::select('id as value', 'name as label')->get();
    }
}
