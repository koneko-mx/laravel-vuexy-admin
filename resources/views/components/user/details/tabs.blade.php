<!-- resources/views/components/user/details/tabs.blade.php -->
@props([
    'tabs' => [
        ['label' => 'Profile', 'icon' => 'ti ti-user-check', 'active' => true, 'url' => '#'],
        ['label' => 'Teams', 'icon' => 'ti ti-users', 'active' => false, 'url' => '#'],
        ['label' => 'Projects', 'icon' => 'ti ti-layout-grid', 'active' => false, 'url' => '#'],
        ['label' => 'Connections', 'icon' => 'ti ti-link', 'active' => false, 'url' => '#'],
    ],
])

<div class="row">
  <div class="col-md-12">
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-2 gap-lg-0">
        @foreach ($tabs as $tab)
          <li class="nav-item">
            <a href="{{ $tab['url']?? 'javascript:;' }}"
               class="nav-link {{ $tab['active']?? false ? 'active' : '' }} waves-effect waves-light">
              <i class="ti-sm {{ $tab['icon']?? 'ti ti-user-check' }} me-1_5"></i> {{ $tab['label']?? 'No label' }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
