@props([
    'items' => [
        [
            'icon' => 'primary',
            'title' => '12 Invoices have been paid',
            'time' => '12 min ago',
            'description' => 'Invoices have been paid to the company',
            'attachment' => [
                'icon' => 'pdf',
                'label' => 'invoices.pdf'
            ]
        ],
        [
            'icon' => 'success',
            'title' => 'Client Meeting',
            'time' => '45 min ago',
            'description' => 'Project meeting with john @10:15am',
            'user' => [
                'avatar' => 'http://vuexy-vite.test/assets/img/avatars/1.png',
                'name' => 'Lester McCarthy',
                'role' => 'CEO of Pixinvent'
            ]
        ],
        [
            'icon' => 'info',
            'title' => 'Create a new project for client',
            'time' => '2 Days Ago',
            'description' => '6 team members in a project',
            'users' => [
                ['avatar' => 'http://vuexy-vite.test/assets/img/avatars/1.png', 'name' => 'Vinnie Mostowy'],
                ['avatar' => 'http://vuexy-vite.test/assets/img/avatars/4.png', 'name' => 'Allen Rieske'],
                ['avatar' => 'http://vuexy-vite.test/assets/img/avatars/2.png', 'name' => 'Julee Rossignol'],
                ['avatar' => null, 'initials' => '+3']
            ]
        ]
    ]
])

<div class="card card-action mb-6">
    <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0">
            <i class="ti ti-chart-bar ti-lg text-body me-4"></i>Activity Timeline
        </h5>
    </div>
    <div class="card-body pt-3">
        <ul class="timeline mb-0">
            @foreach($items as $item)
                <li class="timeline-item timeline-item-transparent">
                    <span class="timeline-point timeline-point-{{ $item['icon'] }}"></span>
                    <div class="timeline-event">
                        <div class="timeline-header mb-3">
                            <h6 class="mb-0">{{ $item['title'] }}</h6>
                            <small class="text-muted">{{ $item['time'] }}</small>
                        </div>
                        <p class="mb-2">{{ $item['description'] }}</p>

                        @isset($item['attachment'])
                            <div class="d-flex align-items-center mb-2">
                                <div class="badge bg-lighter rounded d-flex align-items-center">
                                    <img src="http://vuexy-vite.test/assets/img/icons/misc/{{ $item['attachment']['icon'] }}.png" alt="icon" width="15" class="me-2">
                                    <span class="h6 mb-0 text-body">{{ $item['attachment']['label'] }}</span>
                                </div>
                            </div>
                        @endisset

                        @isset($item['user'])
                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex flex-wrap align-items-center mb-50">
                                    <div class="avatar avatar-sm me-3">
                                        <img src="{{ $item['user']['avatar'] }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div>
                                        <p class="mb-0 small fw-medium">{{ $item['user']['name'] }}</p>
                                        <small>{{ $item['user']['role'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endisset

                        @isset($item['users'])
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                            @foreach($item['users'] as $u)
                                                <li class="avatar pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $u['name'] ?? '' }}">
                                                    @if(isset($u['avatar']))
                                                        <img class="rounded-circle" src="{{ $u['avatar'] }}" alt="Avatar">
                                                    @else
                                                        <span class="avatar-initial rounded-circle pull-up text-heading">{{ $u['initials'] }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        @endisset

                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
