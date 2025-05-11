<!-- resources/views/components/user/details/connections.blade.php -->
@props([
  'connections' => [
    ['avatar' => 'assets/img/avatars/2.png', 'name' => 'Cecilia Payne', 'count' => '45', 'connected' => true],
    ['avatar' => 'assets/img/avatars/3.png', 'name' => 'Curtis Fletcher', 'count' => '1.32k', 'connected' => false],
    ['avatar' => 'assets/img/avatars/10.png', 'name' => 'Alice Stone', 'count' => '125', 'connected' => false],
    ['avatar' => 'assets/img/avatars/7.png', 'name' => 'Darrell Barnes', 'count' => '456', 'connected' => true],
    ['avatar' => 'assets/img/avatars/12.png', 'name' => 'Eugenia Moore', 'count' => '1.2k', 'connected' => true],
  ]
])

<div class="card card-action mb-6">
  <div class="card-header align-items-center">
    <h5 class="card-action-title mb-0">Connections</h5>
    <div class="card-action-element">
      <div class="dropdown">
        <button type="button" class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0 text-muted waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="ti ti-dots-vertical ti-md text-muted"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item waves-effect" href="#">Share connections</a></li>
          <li><a class="dropdown-item waves-effect" href="#">Suggest edits</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item waves-effect" href="#">Report bug</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="card-body">
    <ul class="list-unstyled mb-0">
      @foreach ($connections as $connection)
        <li class="mb-4">
          <div class="d-flex align-items-center">
            <div class="d-flex align-items-center">
              <div class="avatar me-2">
                <img src="{{ asset($connection['avatar']) }}" alt="Avatar" class="rounded-circle">
              </div>
              <div class="me-2">
                <h6 class="mb-0">{{ $connection['name'] }}</h6>
                <small>{{ $connection['count'] }} Connections</small>
              </div>
            </div>
            <div class="ms-auto">
              <button class="btn {{ $connection['connected'] ? 'btn-label-primary' : 'btn-primary' }} btn-icon waves-effect waves-light">
                <i class="ti {{ $connection['connected'] ? 'ti-user-check' : 'ti-user-x' }} ti-md"></i>
              </button>
            </div>
          </div>
        </li>
      @endforeach
      <li class="text-center">
        <a href="javascript:void(0);">View all connections</a>
      </li>
    </ul>
  </div>
</div>
