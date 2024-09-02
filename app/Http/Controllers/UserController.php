<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required|email|unique:users,id',
        ]);
        $password =  Hash::make('password');

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_name' => $request->user_name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'password' =>  $password,
        ]);

        if ($user) {
            $role = Role::where('name', 'admin')->first();
            $user->roles()->attach($role);
            session()->flash('success', 'Item Created Successfully');
            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } else {
            session()->flash('error', 'Cannot Create The Item');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  User $user)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required|email|unique:users',
        ]);
        $password =  Hash::make('password');
        // $user = User::where('id', $request->id)->first();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_name' => $request->user_name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'password' =>  $password,
        ]);
        $is_updated = $user->wasChanged('first_name', 'last_name', 'user_name', 'mobile_number', 'email', 'password');
        if ($is_updated) {
            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!empty($user->id)) {
            $is_deleted = $user->delete();
            if ($is_deleted) {
                return redirect()->route('users.index')
                    ->with('success', 'User deleted successfully.');
            }
        }
    }
}
