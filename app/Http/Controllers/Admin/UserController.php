<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request) {
        $q = User::with('roles')->latest();
        if ($request->filled('role')) $q->where('role',$request->role);
        if ($request->filled('search')) $q->where(fn($qq)=>$qq->where('name','like','%'.$request->search.'%')->orWhere('email','like','%'.$request->search.'%'));
        return view('admin.users.index', ['users'=>$q->paginate(20)->withQueryString(), 'roles'=>Role::orderBy('name')->get()]);
    }
    public function create(){ return view('admin.users.create',['roles'=>Role::orderBy('name')->get()]); }
    public function store(Request $r){
        $d=$r->validate(['name'=>['required','string','max:255'],'email'=>['required','email','unique:users'],'phone'=>['nullable','string','max:30'],'role'=>['required','in:super_admin,admin,staff,applicant'],'password'=>['required','string','min:8','confirmed'],'is_active'=>['sometimes','boolean']]);
        $d['password']=Hash::make($d['password']); $d['is_active']=$r->boolean('is_active', true);
        $user=User::create($d);
        if ($r->filled('roles')) $user->roles()->sync($r->input('roles'));
        return redirect()->route('admin.users.index')->with('success','User created.');
    }
    public function edit(User $user){ return view('admin.users.edit',['user'=>$user,'roles'=>Role::orderBy('name')->get()]); }
    public function update(Request $r, User $user){
        $d=$r->validate(['name'=>['required','string','max:255'],'email'=>['required','email',Rule::unique('users')->ignore($user->id)],'phone'=>['nullable','string','max:30'],'role'=>['required','in:super_admin,admin,staff,applicant'],'is_active'=>['sometimes','boolean'],'password'=>['nullable','string','min:8','confirmed']]);
        if (empty($d['password'])) unset($d['password']); else $d['password']=Hash::make($d['password']);
        $d['is_active']=$r->boolean('is_active');
        $user->update($d);
        if ($r->has('roles')) $user->roles()->sync($r->input('roles',[]));
        return redirect()->route('admin.users.index')->with('success','Updated.');
    }
    public function destroy(User $user){
        if ($user->id===auth()->id()) return back()->with('error','You cannot delete yourself.');
        $user->delete();
        return back()->with('success','Deleted.');
    }
}