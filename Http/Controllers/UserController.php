<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth,Validator};
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Services\AvatarImageService;
use Koneko\VuexyAdmin\Queries\GenericQueryBuilder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bootstrapTableIndexConfig = [
                'table' => 'users',
                'columns' => [
                    'users.id',
                    'users.name AS full_name',
                    'users.email',
                    'users.email_verified_at',
                    'users.profile_photo_path',
                    'users.status',
                    'users.created_by',
                    'users.created_at',
                    'users.updated_at',
                ],
                'filters' => [
                    'search' => ['users.code', 'users.full_name', 'users.email', 'parent_name'],
                ],
                'sort_column' => 'users.full_name',
                'default_sort_order' => 'asc',
            ];

            return (new GenericQueryBuilder($request, $bootstrapTableIndexConfig))->getJson();
        }

        return view('vuexy-admin::users.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:1024',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);

        // Preparamos los datos
        $user_request = array_merge_recursive($request->all(), [
            'remember_token' => Str::random(10),
            'created_by' => Auth::user()->id,
        ]);

        $user_request['password'] = bcrypt($request->password);

        // Guardamos el nuevo usuario
        $user = User::create($user_request);

        // Asignmos los permisos
        $user->assignRole($request->roles);

        // Asignamos Sucursals
        //$user->stores()->attach($request->stores);

        if ($request->file('photo')){
            app(AvatarImageService::class)->updateProfilePhoto($user, $request->file('photo'));
        }

        return response()->json(['success' => 'Se agrego correctamente el usuario']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('vuexy-admin::users.show', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  User $user
     * @return \Illuminate\Http\Response
     */
    public function updateAjax(Request $request, User $user)
    {
        // Validamos los datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => "required|max:191|unique:users,email," . $user->id,
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);

        // Preparamos los datos
        $user_request = $request->all();

        if ($request->password) {
            $user_request['password'] = bcrypt($request->password);
        } else {
            unset($user_request['password']);
        }

        // Guardamos los cambios
        $user->update($user_request);

        // Sincronizamos Roles
        $user->syncRoles($request->roles);

        // Sincronizamos Sucursals
        //$user->stores()->sync($request->stores);

        // Actualizamos foto de perfil
        if ($request->file('photo')){
            app(AvatarImageService::class)->updateProfilePhoto($user, $request->file('photo'));
        }

        return response()->json(['success' => 'Se guardo correctamente los cambios.']);
    }


    public function userSettings(User $user)
    {
        return view('vuexy-admin::users.user-settings', compact('user'));
    }

}
