<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Users\UsersTableConfigBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarInitialsService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly AvatarInitialsService $avatarService
    ) {}


    /**
     * Display a listing of the resource (Bootstrap Table AJAX or View).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\View\View
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(UsersTableConfigBuilder::class)
                ->getQueryBuilder($request)
                ->getJson();
        }

        return view('vuexy-admin::settings.users.index', ['pageConfigs' => ['contentLayout' => 'wide']]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user): View
    {
        return view('vuexy-admin::settings.users.show', compact('user'));
    }

    /**
     * Show the crud for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('vuexy-admin::settings.users.edit', compact('user'));
    }

    /**
     * Show the crud for editing the specified resource.
     */
    public function delete(User $user): View
    {
        return view('vuexy-admin::settings.users.show', compact('user'))->with('mode', 'delete');

    }

    public function generateAvatar(Request $request): BinaryFileResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'color' => 'nullable|string|regex:/^[0-9a-fA-F]{6}$/',
                'background' => 'nullable|string|regex:/^[0-9a-fA-F]{6}$/',
                'size' => 'nullable|integer|min:20|max:1024',
                'max_length' => 'nullable|integer|min:1|max:3'
            ]);

            $response = $this->avatarService->getAvatarImage(
                name: $validated['name'],
                color: $validated['color'] ?? null,
                background: $validated['background'] ?? null,
                size: $validated['size'] ?? null,
                maxLength: $validated['max_length'] ?? null
            );

            $response->headers->set('Cache-Control', 'public, max-age=86400');

            return $response;

        } catch (\Throwable $e) {
            return  $this->avatarService->getAvatarImage(
                name: 'E R R',
                color: '#FF0000',
                maxLength: 3
            );
        }
    }

    public function viewerIndex(User $user): View
    {
        return view('vuexy-admin::user.viewer.index', compact('user'));
    }
}

