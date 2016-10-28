<?php

namespace App\Http\Controllers;

use App\City;
use App\Country;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public static function searchCities($search = null)
    {
        if (!$search) {
            $cities = City::with('country')
                ->where('biggest_city', true)
                ->take(15)
                ->get();
        } else {
            $cities = City::with('country')
                ->where('city', 'LIKE', $search.'%')
                ->take(15)
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $cities
        ], 200);
    }

    public static function searchCountries($search = null)
    {
        $userCountries = DB::table('country_user')
            ->where('user_id', Auth::user()->id)
            ->lists('country_id');

        if (!$search) {
            $countries = Country::take(15)
                ->whereNotIn('id', $userCountries)
                ->get();
        } else {
            $countries = Country::where('name', 'LIKE', $search . '%')
                ->whereNotIn('id', $userCountries)
                ->take(15)
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $countries
        ], 200);
    }
}
