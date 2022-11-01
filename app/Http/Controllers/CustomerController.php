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

    public function getUsers(Request $request)
    {
        if ($request->input('showdata')) {
            return Customer::orderBy('created_at', 'desc')->get();
        }
        $columns = ['name', 'phone', 'created_at'];
        $length = $request->input('length');
        $column = $request->input('column');
        $search_input = $request->input('search');
        $query = Customer::select('name', 'phone', 'created_at')
            ->orderBy($columns[$column]);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('name', 'like', '%' . $search_input . '%')
                    ->orWhere('phone', 'like', '%' . $search_input . '%')
                    ->orWhere('created_at', 'like', '%' . $search_input . '%');
            });
        }
        $users = $query->paginate($length);
        return ['data' => $users];
    }
}
