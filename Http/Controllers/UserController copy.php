<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Services\AvatarImageService;

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
            $users = User::when(!Auth::user()->hasRole('SuperAdmin'), function ($query) {
                $query->where('id', '>', 1);
            })
                ->latest()
                ->get();

            return DataTables::of($users)
                ->only(['id', 'name', 'email', 'avatar', 'roles', 'status', 'created_at'])
                ->addIndexColumn()
                ->addColumn('avatar', function ($user) {
                    return $user->profile_photo_url;
                })
                ->addColumn('roles', function ($user) {
                    return (Arr::pluck($user->roles, ['name']));
                })
                /*
                ->addColumn('stores', function ($user) {
                    return (Arr::pluck($user->stores, ['nombre']));
                })
                y*/
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('Y-m-d');
                })
                ->make(true);
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

    public function generateAvatar(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'name' => 'nullable|string',
            'color' => 'nullable|string|size:6',
            'background' => 'nullable|string|size:6',
            'size' => 'nullable|integer|min:20|max:1024'
        ]);

        $name       = $request->get('name', 'NA');
        $color      = $request->get('color', '7F9CF5');
        $background = $request->get('background', 'EBF4FF');
        $size       = $request->get('size', 100);

        return User::getAvatarImage($name, $color, $background, $size);

        try {
        } catch (\Exception $e) {
            // String base64 de una imagen PNG transparente de 1x1 píxel
            $transparentBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==';

            return response()->make(base64_decode($transparentBase64), 200, [
                'Content-Type' => 'image/png'
            ]);
        }
    }
}
