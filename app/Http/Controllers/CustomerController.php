<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // $customers = Customer::paginate(5);
        // return response()->json($customers);
        if ($request->input('client')) {
            return Customer::select('id', 'name', 'phone')->get();
        }

        $columns = ['id', 'name', 'phone'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = Customer::select('id', 'name', 'phone')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', $searchValue)
                    ->orWhere('phone', $searchValue)
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }
}
