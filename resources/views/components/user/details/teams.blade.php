<!-- resources/views/components/user/details/teams.blade.php -->
@props([
    'teams' => [
        ['name' => 'React Developers', 'members' => 72, 'badge' => 'Developer', 'badgeColor' => 'danger', 'avatar' => 'img/icons/brands/react-label.png'],
        ['name' => 'Support Team', 'members' => 122, 'badge' => 'Support', 'badgeColor' => 'primary', 'avatar' => 'img/icons/brands/support-label.png'],
        ['name' => 'UI Designers', 'members' => 7, 'badge' => 'Designer', 'badgeColor' => 'info', 'avatar' => 'img/icons/brands/figma-label.png'],
        ['name' => 'Vue.js Developers', 'members' => 289, 'badge' => 'Developer', 'badgeColor' => 'danger', 'avatar' => 'img/icons/brands/vue-label.png'],
        ['name' => 'Digital Marketing', 'members' => 24, 'badge' => 'Marketing', 'badgeColor' => 'secondary', 'avatar' => 'img/icons/brands/twitter-label.png'],
    ],
])

<div class="card card-action mb-6">
  <div class="card-header align-items-center">
    <h5 class="card-action-title mb-0">Teams</h5>
    <div class="card-action-element">
      <div class="dropdown">
        <button type="button" class="btn btn-icon btn-text-secondary dropdown-toggle hide-arrow p-0 waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="ti ti-dots-vertical text-muted"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Share teams</a></li>
          <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Suggest edits</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Report bug</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="card-body">
    <ul class="list-unstyled mb-0">
      @foreach ($teams as $team)
        <li class="mb-4">
          <div class="d-flex align-items-center">
            <div class="d-flex align-items-center">
              <div class="avatar me-2">
                <img src="{{ asset($team['avatar']) }}" alt="Avatar" class="rounded-circle">
              </div>
              <div class="me-2">
                <h6 class="mb-0">{{ $team['name'] }}</h6>
                <small>{{ $team['members'] }} Members</small>
              </div>
            </div>
            <div class="ms-auto">
              <a href="javascript:;">
                <span class="badge bg-label-{{ $team['badgeColor'] }}">{{ $team['badge'] }}</span>
              </a>
            </div>
          </div>
        </li>
      @endforeach
      <li class="text-center">
        <a href="javascript:;">View all teams</a>
      </li>
    </ul>
  </div>
</div>
