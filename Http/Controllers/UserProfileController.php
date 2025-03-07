<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Koneko\VuexyAdmin\Services\AvatarInitialsService;
use Koneko\VuexyAdmin\Models\User;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('vuexy-admin::profile.index');
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

        $avatarService = new AvatarInitialsService();

        try {
            return $avatarService->getAvatarImage($name, $color, $background, $size);

        } catch (\Exception $e) {
            // String base64 de una imagen PNG transparente de 1x1 píxel
            $transparentBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==';

            return response()->make(base64_decode($transparentBase64), 200, [
                'Content-Type' => 'image/png'
            ]);
        }
    }


}
