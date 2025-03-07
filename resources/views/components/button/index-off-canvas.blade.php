@php
    use Illuminate\Support\Str;
@endphp

@props([
    'label'   => '',
    'tagName' => '',
    'icon'    => 'ti ti-pencil-plus',
])

@php
    $tagOffcanvas = ucfirst(Str::camel($tagName));
    $helperTag    = Str::kebab($tagName);

    $ariaControls = "'offcanvas{$tagOffcanvas}";
    $dataBsToggle = 'offcanvas';
    $dataBsTarget = "#offcanvas{$tagOffcanvas}";
    $onclick      = "window.formHelpers['{$helperTag}'].reloadOffcanvas('create')";
@endphp

<button
    type="button"
    class="btn btn-primary waves-effect waves-light"
    tabindex="0"
    aria-controls='{{ $ariaControls }}'
    data-bs-toggle="{{ $dataBsToggle }}"
    data-bs-target="{{ $dataBsTarget }}"
    onclick="{!! $onclick !!}">
    <span class="ti-xs {{ $icon }} me-2"></span>{{ ucfirst($label) }}
</button>
