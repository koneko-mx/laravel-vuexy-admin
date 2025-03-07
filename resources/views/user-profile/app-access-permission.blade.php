@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Permission - Apps')

@section('vendor-style')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss',
])
@endsection

@section('vendor-script')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js',
  ])
@endsection

@push('page-script')
@vite([
  'Resources/js/app-access-permission.js',
  'Resources/js/modal-add-permission.js',
  'Resources/js/modal-edit-permission.js',
  ])
@endpush

@section('content')


<!-- Permission Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-permissions table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>Name</th>
          <th>Assigned To</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Permission Table -->

<!-- Modal -->
@include('_partials/_modals/modal-add-permission')
@include('_partials/_modals/modal-edit-permission')
<!-- /Modal -->
@endsection
