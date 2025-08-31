@once
@push('page-script')
<script>
  // Evita redefinir si la vista se incluye más de una vez
  if (!window.imageProcessor) {
    window.imageProcessor = (initial) => ({
      nf: new Intl.NumberFormat(),

      uid: 'ip-' + Math.random().toString(36).slice(2),

      // estado (Livewire + local)
      mode: initial.mode,
      fit: initial.fit,
      targetAspect: initial.targetAspect,
      pixelArea: initial.pixelArea,
      format: initial.format,
      quality: initial.quality,
      sourceAspect: initial.sourceAspect,

      // input local para “Personalizada”
      aspectInput: null,

      // utils
      num(v){ v = Number(v); return Number.isFinite(v) ? v : 0; },

      // computados
      get effectiveAspect(){
        const src = this.num(this.sourceAspect);
        const tgt = this.num(this.targetAspect) || 1.91;
        return (this.fit === 'keep' && src > 0) ? src : tgt;
      },
      get pixelAreaNum(){ return this.num(this.pixelArea) || 756000; },
      get outW(){
        if (this.fit === 'keep' && !this.sourceAspect) return 0;
        const a = Math.max(0.1, this.num(this.effectiveAspect));
        return Math.round(Math.sqrt(this.pixelAreaNum * a));
      },
      get outH(){
        if (this.fit === 'keep' && !this.sourceAspect) return 0;
        const a = Math.max(0.1, this.num(this.effectiveAspect));
        return Math.max(1, Math.round(this.outW / a));
      },

      init(){
        // normaliza
        this.targetAspect = this.num(this.targetAspect) || 1.91;
        this.pixelArea    = this.pixelAreaNum;
        this.quality      = this.num(this.quality) || 82;
        if (!Number.isFinite(Number(this.sourceAspect))) this.sourceAspect = null;

        // set inicial del input visible
        this.aspectInput = this.num(this.targetAspect) || 1.91;

        this.$watch('fit', v => {
          if (v === 'keep' && this.num(this.sourceAspect) > 0) {
            this.aspectInput = Number(this.num(this.sourceAspect).toFixed(2));
          } else {
            this.aspectInput = this.num(this.targetAspect) || 1.91;
          }
        });

        this.$watch('sourceAspect', v => {
          if (this.fit === 'keep' && this.num(v) > 0) {
            this.aspectInput = Number(this.num(v).toFixed(2));
          }
        });

        this.$watch('aspectInput', v => {
          if (this.fit !== 'keep') {
            const n = this.num(v) || 1.91;
            this.targetAspect = n;
          }
        });

        this.$watch('targetAspect', v => {
          if (this.fit !== 'keep') {
            this.aspectInput = this.num(v) || 1.91;
          }
        });
      }
    });
  }
</script>
@endpush
@endonce
