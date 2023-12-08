<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\v1\users\CreateUserRequest;
use App\Http\Requests\v1\users\UpdateUserRequest;
use App\Http\Requests\v1\users\UpdatePasswordUserRequest;

use App\Http\Resources\v1\users\UserResource;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return $request->all();
        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText );

        $atr_surname = [];
        $atr_name = [];

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        foreach ($val as $q) {
            array_push($atr_name, [DB::raw('CONCAT(name, " ",surname)'), 'LIKE', '%'.strtolower($q).'%'] );
            //array_push($atr_surname, ['surname', 'LIKE', '%'.strtolower($q).'%'] );
            
        };

        $users = User::orderBy('name', 'ASC')
            ->where($atr_name);
        
        if ( $request->has('is_empleados') && boolval($request->get('is_empleados'))) {
            $users = $users->whereNotNull('role');
        }

        if ( $request->has('is_empleados') && !boolval($request->get('is_empleados')) ) {
            $users = $users->whereNull('role');
        }

        if ( $request->has('paginate')) {
            $paginate = $request->get('paginate');
            $users = $users->paginate($limit); 
        }else {
            $users = $users->get();
        }

        return UserResource::collection($users);
        

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function store(CreateUserRequest $request)
    public function store(CreateUserRequest $request)
    {
        $data = $request->get('data');

        $user = User::where('email', '=', $request->input('data.attributes.email'))->first();

        if ( $user != null ) {
            return response()->json(['message' => 'El email ya se encuentra registrado'], 422);
        }
        
        $user = User::create([
            'name' => $request->input('data.attributes.name'),
            'surname' => $request->input('data.attributes.surname'),
            'email' => $request->input('data.attributes.email'),
            'password' => Hash::make($request->input('data.attributes.password')),
            'tipo_persona' => 'FISICA',
        ]);

        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user_mail = User::where('email', '=', $request->input('data.attributes.email'))->first();

        if ( $user_mail != null && $user_mail->id != $user->id ) {
            return response()->json(['message' => 'El email ya se encuentra registrado'], 422);
        }

        $user->update($request->input('data.attributes'));

        $user->role = $request->input('data.attributes.role');

        $user->save();

        return new UserResource($user);
    }

    public function update_password(UpdatePasswordUserRequest $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        //return $request->input('data.attributes.password');

        $user->password = Hash::make($request->input('data.attributes.password'));

        $user->update([
            'password' => Hash::make($request->input('data.attributes.password'))
        ]);

        //$user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
