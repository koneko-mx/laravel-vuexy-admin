<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth,DB,Validator};
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
                    'users.code',
                    DB::raw("CONCAT_WS(' ', users.name, users.last_name) AS full_name"),
                    'users.email',
                    'users.birth_date',
                    'users.hire_date',
                    'users.curp',
                    'users.nss',
                    'users.job_title',
                    'users.profile_photo_path',
                    DB::raw("(SELECT GROUP_CONCAT(roles.name SEPARATOR ';') as roles FROM model_has_roles INNER JOIN roles ON (model_has_roles.role_id = roles.id) WHERE model_has_roles.model_id = 1) as roles"),
                    'users.is_partner',
                    'users.is_employee',
                    'users.is_prospect',
                    'users.is_customer',
                    'users.is_provider',
                    'users.is_user',
                    'users.status',
                    DB::raw("CONCAT_WS(' ', created.name, created.last_name) AS creator"),
                    'created.email AS creator_email',
                    'users.created_at',
                    'users.updated_at',
                ],
                'joins' => [
                    [
                        'table' => 'users as parent',
                        'first' => 'users.parent_id',
                        'second' => 'parent.id',
                        'type' => 'leftJoin',
                    ],
                    [
                        'table' => 'users as agent',
                        'first' => 'users.agent_id',
                        'second' => 'agent.id',
                        'type' => 'leftJoin',
                    ],
                    [
                        'table' => 'users as created',
                        'first' => 'users.created_by',
                        'second' => 'created.id',
                        'type' => 'leftJoin',
                    ],
                    [
                        'table' => 'sat_codigo_postal',
                        'first' => 'users.domicilio_fiscal',
                        'second' => 'sat_codigo_postal.c_codigo_postal',
                        'type' => 'leftJoin',
                    ],
                    [
                        'table' => 'sat_estado',
                        'first' => 'sat_codigo_postal.c_estado',
                        'second' => 'sat_estado.c_estado',
                        'type' => 'leftJoin',
                        'and' => [
                            'sat_estado.c_pais = "MEX"',
                        ],
                    ],
                    [
                        'table' => 'sat_localidad',
                        'first' => 'sat_codigo_postal.c_localidad',
                        'second' => 'sat_localidad.c_localidad',
                        'type' => 'leftJoin',
                        'and' => [
                            'sat_codigo_postal.c_estado = sat_localidad.c_estado',
                        ],
                    ],
                    [
                        'table' => 'sat_municipio',
                        'first' => 'sat_codigo_postal.c_municipio',
                        'second' => 'sat_municipio.c_municipio',
                        'type' => 'leftJoin',
                        'and' => [
                            'sat_codigo_postal.c_estado = sat_municipio.c_estado',
                        ],
                    ],
                    [
                        'table' => 'sat_regimen_fiscal',
                        'first' => 'users.c_regimen_fiscal',
                        'second' => 'sat_regimen_fiscal.c_regimen_fiscal',
                        'type' => 'leftJoin',
                    ],
                    [
                        'table' => 'sat_uso_cfdi',
                        'first' => 'users.c_uso_cfdi',
                        'second' => 'sat_uso_cfdi.c_uso_cfdi',
                        'type' => 'leftJoin',
                    ],
                ],
                'filters' => [
                    'search' => ['users.name', 'users.email', 'users.code', 'parent.name', 'created.name'],
                ],
                'sort_column' => 'users.name',
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
            $avatarImageService = new AvatarImageService();

            $avatarImageService->updateProfilePhoto($user, $request->file('photo'));
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
        if ($request->file('photo'))
            $avatarImageService = new AvatarImageService();

            $avatarImageService->updateProfilePhoto($user, $request->file('photo'));

        return response()->json(['success' => 'Se guardo correctamente los cambios.']);
    }


    public function userSettings(User $user)
    {
        return view('vuexy-admin::users.user-settings', compact('user'));
    }

}
