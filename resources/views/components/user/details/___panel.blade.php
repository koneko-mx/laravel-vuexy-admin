<!-- resources/views/components/user/details/panel.blade.php -->
@props([
    'user' => null,
    'tabs' => [],
    'stats' => [],
    'timeline' => [],
    'connections' => [],
    'teams' => [],
])

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Banner y Header -->
    <x-vuexy-admin::user.details.banner-profile-header
        :banner-url="$user?->banner_url"
        :avatar-url="$user?->profile_photo_url"
        :name="$user?->name"
        :position="$user?->position"
        :location="$user?->location"
        :joined="$user?->joined_at?->format('F Y')"
        button-label="Connected"
        button-icon="ti ti-user-check"
    />

    <!-- Tabs -->
    <x-vuexy-admin::user.details.tabs :tabs="$tabs" />

    <!-- Contenido Principal -->
    <div class="row">
        <!-- Columna Izquierda -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <x-vuexy-admin::user.details.about :user="$user" class="mb-6" />
            <x-vuexy-admin::user.details.overview :stats="$stats" />
        </div>

        <!-- Columna Derecha -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <x-vuexy-admin::user.details.timeline :items="$timeline" class="mb-6" />
            <div class="row">
                <div class="col-lg-12 col-xl-6">
                    <x-vuexy-admin::user.details.connections :connections="$connections" />
                </div>
                <div class="col-lg-12 col-xl-6">
                    <x-vuexy-admin::user.details.teams :teams="$teams" />
                </div>
            </div>
        </div>
    </div>
</div>
