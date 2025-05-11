<!-- resources/views/components/user/details/overview.blade.php -->

<div class="card mb-6">
    <div class="card-body">
      <small class="card-text text-uppercase text-muted small">Overview</small>
      <ul class="list-unstyled mb-0 mt-3 pt-1">
        <li class="d-flex align-items-end mb-4">
          <i class="ti ti-check ti-lg"></i>
          <span class="fw-medium mx-2">Task Compiled:</span>
          <span>{{ $tasksCompiled ?? '13.5k' }}</span>
        </li>
        <li class="d-flex align-items-end mb-4">
          <i class="ti ti-layout-grid ti-lg"></i>
          <span class="fw-medium mx-2">Projects Compiled:</span>
          <span>{{ $projectsCompiled ?? '146' }}</span>
        </li>
        <li class="d-flex align-items-end">
          <i class="ti ti-users ti-lg"></i>
          <span class="fw-medium mx-2">Connections:</span>
          <span>{{ $connections ?? '897' }}</span>
        </li>
      </ul>
    </div>
  </div>
