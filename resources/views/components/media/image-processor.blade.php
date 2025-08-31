@props([
  // NOMBRES de las props Livewire (strings)
  'modeProp'         => 'og_mode',
  'fitProp'          => 'image_fit',
  'targetAspectProp' => 'target_aspect',
  'pixelAreaProp'    => 'pixel_area',
  'formatProp'       => 'image_format',
  'qualityProp'      => 'image_quality',
  'sourceAspectProp' => 'source_aspect',
])

<div {{ $attributes->merge(['class' => 'row g-3']) }}
    x-data="imageProcessor({
        mode: @entangle($modeProp),
        fit: @entangle($fitProp),
        targetAspect: @entangle($targetAspectProp).defer,
        pixelArea: @entangle($pixelAreaProp).defer,
        format: @entangle($formatProp),
        quality: @entangle($qualityProp),
        sourceAspect: @entangle($sourceAspectProp),
    })" x-init="init()" x-cloak>

  {{-- Col 2: Fit --}}
  <div class="col-lg-3">
    <label class="form-label d-block">Ajuste de imagen</label>
    <div class="row">
      <div class="col-lg-12 col-sm-6">
        <div class="form-check mb-1">
          <input class="form-check-input" type="radio" :id="uid + '-fit_cover'" value="cover"
                 x-model="fit" :disabled="mode !== 'override'">
          <label class="form-check-label" :for="uid + '-fit_cover'">Recortar (cover)</label>
        </div>
      </div>
      <div class="col-lg-12 col-sm-6">
        <div class="form-check mb-1">
          <input class="form-check-input" type="radio" :id="uid + '-fit_keep'" value="keep"
                 x-model="fit" :disabled="mode !== 'override'">
          <label class="form-check-label" :for="uid + '-fit_keep'">Conservar aspecto</label>
        </div>
      </div>
      <div class="col-12">
        <small class="text-muted mt-1">
          Presupuesto: <span x-text="nf.format(pixelAreaNum)"></span> px²
        </small>
      </div>
    </div>
  </div>

  {{-- Col 1: Relación de aspecto --}}
  <div class="col-lg-4">
    <label class="form-label">Relación de aspecto destino</label>

    {{-- solo opacamos si no es override; los botones quedan deshabilitados en keep --}}
    <div class="d-flex gap-2 flex-wrap mb-1" :class="{ 'opacity-50': mode !== 'override' }">
      <button type="button" class="btn btn-outline-secondary btn-sm"
              :class="{ 'active': Math.abs(Number(targetAspect) - 1.91) < 0.02 }"
              :disabled="mode !== 'override' || fit === 'keep'"
              @click="targetAspect = 1.91">
        1.91:1 (recomendado)
      </button>

      <button type="button" class="btn btn-outline-secondary btn-sm"
              :class="{ 'active': Math.abs(Number(targetAspect) - 1.0) < 0.02 }"
              :disabled="mode !== 'override' || fit === 'keep'"
              @click="targetAspect = 1.0">
        1:1 (cuadrada)
      </button>

      <div class="input-group input-group-sm" style="width:220px;">
        <span class="input-group-text">Personalizada</span>
        <input type="number" step="0.01" min="0.10" max="10" class="form-control"
               x-model.number="aspectInput"
               :readonly="fit === 'keep' && !!sourceAspect"
               :disabled="mode !== 'override'"
               @input="if(fit!=='keep'){ targetAspect = num(aspectInput) || 1.91 }"
               @change="if(!+aspectInput) { aspectInput = 1.91; if(fit!=='keep') targetAspect = 1.91 }"
               placeholder="1.91">
      </div>
    </div>

    {{-- salida aprox; si aún no hay sourceAspect en keep, mostramos guiones --}}
    <div class="col-12">
      <small class="text-muted mt-1">
        Salida aprox.:
        <span x-text="outW ? nf.format(outW) : '—'"></span>
        ×
        <span x-text="outH ? nf.format(outH) : '—'"></span>
        px
      </small>
    </div>
  </div>

  {{-- Col 3: Formato/Calidad --}}
  <div class="col-lg-5">
    <div class="row">
      <div class="col-sm-7 col-lg-12 mb-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text">Formato</span>
          <select class="form-select" x-model="format" :disabled="mode !== 'override'">
            <option value="auto">Automático (mantener)</option>
            <option value="jpg">JPEG</option>
            <option value="png">PNG</option>
            <option value="webp">WebP</option>
          </select>
        </div>
      </div>
      <div class="col-sm-5 col-lg-8 mb-1">
        <div class="input-group input-group-sm">
          <span class="input-group-text">Calidad</span>
          <input type="number" min="60" max="95" class="form-control"
                 x-model.number="quality" :disabled="mode !== 'override'">
        </div>
      </div>
    </div>
    <small class="text-muted d-block mt-1">Fotos: JPEG/WebP ~80–82. Transparencia: PNG/WebP.</small>
  </div>
</div>
