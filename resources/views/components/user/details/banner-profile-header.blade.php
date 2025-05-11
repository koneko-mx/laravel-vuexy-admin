@props([
    'bannerUrl' => null,
    'avatarUrl' => null,
    'name' => 'Nombre no definido',
    'position' => null,
    'location' => null,
    'joined' => null,
    'buttonLabel' => null,
    'buttonIcon' => null,
    'buttonClass' => 'btn btn-primary mb-1 waves-effect waves-light',
    'buttonAction' => 'javascript:void(0)'
])

<div class="card mb-6">
    {{-- Banner superior --}}
    <div class="user-profile-header-banner">
        @if ($bannerUrl)
            <img src="{{ $bannerUrl }}" alt="Banner image" class="rounded-top">
        @else
            <div class="bg-secondary rounded-top" style="height: 200px;"></div>
        @endif
    </div>

    {{-- Contenido principal del perfil --}}
    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
            <img src="{{ $avatarUrl }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                <div class="user-profile-info">
                    <h4 class="mb-2 mt-lg-6">{{ $name }}</h4>
                    <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                        @if ($position)
                            <li class="list-inline-item d-flex gap-2 align-items-center">
                                <i class="ti ti-palette ti-lg"></i><span class="fw-medium">{{ $position }}</span>
                            </li>
                        @endif

                        @if ($location)
                            <li class="list-inline-item d-flex gap-2 align-items-center">
                                <i class="ti ti-map-pin ti-lg"></i><span class="fw-medium">{{ $location }}</span>
                            </li>
                        @endif

                        @if ($joined)
                            <li class="list-inline-item d-flex gap-2 align-items-center">
                                <i class="ti ti-calendar ti-lg"></i><span class="fw-medium">Joined {{ $joined }}</span>
                            </li>
                        @endif
                    </ul>
                </div>

                @if ($buttonLabel)
                    <a href="{{ $buttonAction }}" class="{{ $buttonClass }}">
                        @if ($buttonIcon)
                            <i class="{{ $buttonIcon }} ti-xs me-2"></i>
                        @endif
                        {{ $buttonLabel }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>




<!--
<div class="card mb-6">



    <div class="user-profile-header-banner">
      <img src="http://vuexy-vite.test/assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top">
    </div>



    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">


      <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
        <img src="http://vuexy-vite.test/assets/img/avatars/1.png" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img">
      </div>


      <div class="flex-grow-1 mt-3 mt-lg-5">
        <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
          <div class="user-profile-info">
            <h4 class="mb-2 mt-lg-6">John Doe</h4>
            <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
              <li class="list-inline-item d-flex gap-2 align-items-center">
                <i class="ti ti-palette ti-lg"></i><span class="fw-medium">UX Designer</span>
              </li>
              <li class="list-inline-item d-flex gap-2 align-items-center">
                <i class="ti ti-map-pin ti-lg"></i><span class="fw-medium">Vatican City</span>
              </li>
              <li class="list-inline-item d-flex gap-2 align-items-center">
                <i class="ti ti-calendar ti-lg"></i><span class="fw-medium"> Joined April 2021</span></li>
            </ul>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary mb-1 waves-effect waves-light">
            <i class="ti ti-user-check ti-xs me-2"></i>Connected
          </a>
        </div>
      </div>


    </div>




  </div>
-->
