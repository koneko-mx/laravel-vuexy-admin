// resources/assets/js/forms/formCustomListener.js
export default class FormCustomListener {
  constructor(config = {}) {
    const defaults = {
      formSelector: '.form-custom-listener',
      dispatchOnSubmit: null,     // método Livewire a invocar (p.ej. "save")
      fieldsValidation: null,     // definición para FormValidation
      useSubmitPlugin: true,      // usa plugins.SubmitButton si está disponible
      debug: false,
    };
    this.config = { ...defaults, ...config };

    this._inst = null;            // instancia de FormValidation
    this._formEl = null;          // <form> actual
    this._isSubmitting = false;   // flag UI
    this._currentHostBtn = null;  // botón .btn-save si aplica
    this._componentId = null;     // wire:id del componente Livewire
    this._hooked = false;         // hooks Livewire v3 ya registrados

    this._boot();
  }

  /* ---------- ciclo de vida ---------- */

  _boot() {
    const form = document.querySelector(this.config.formSelector);
    if (!form) return;

    // wire:id actual del componente que contiene el form
    this._componentId = form.closest('[wire\\:id]')?.getAttribute('wire:id') || null;

    // Guarda referencia y monta validación/bindings
    this._formEl = form;
    this._ensureInit(form);

    // Registra hooks v3 cuando Livewire esté listo
    if (window.Livewire?.hook) {
      this._registerHooksV3();
    } else {
      document.addEventListener('livewire:init', () => this._registerHooksV3(), { once: true });
    }

    if (this.config.debug) console.log('[FCL] boot ok →', this._componentId);
  }

  _ensureInit(form) {
    // Siempre re‐montamos porque Livewire puede haber reemplazado el nodo
    form.removeAttribute('data-initialized');
    this._bind(form);
    this._initValidation(form);
    form.dataset.initialized = 'true';
  }

  _registerHooksV3() {
    if (this._hooked) return;

    // 1) commit: se ejecuta al final del ciclo si el mensaje fue exitoso
    Livewire.hook('commit', ({ component, succeed }) => {
      if (!component) return;
      if (component.id === this._componentId) {
        if (this.config.debug) console.log('[FCL] commit', { id: component.id, succeed });
        this._unlockAfterLivewire();
      }
    });

    // 2) message.failed: errores del request
    Livewire.hook('message.failed', ({ component }) => {
      if (!component) return;
      if (component.id === this._componentId) {
        if (this.config.debug) console.warn('[FCL] message.failed', component.id);
        this._unlockAfterLivewire();
      }
    });

    // 3) morph.updated: el DOM del componente fue reconciliado
    Livewire.hook('morph.updated', ({ component /*, el */ }) => {
      if (!component) return;
      if (component.id === this._componentId) {
        // El <form> puede haber sido reemplazado: vuelve a tomar referencia, id y validación
        this._formEl = document.querySelector(this.config.formSelector);
        this._componentId =
          this._formEl?.closest('[wire\\:id]')?.getAttribute('wire:id') || this._componentId;

        if (this.config.debug) console.log('[FCL] morph.updated → rebind/revalidate', this._componentId);
        this.reloadValidation();
      }
    });

    this._hooked = true;
    if (this.config.debug) console.log('[FCL] hooks v3 registrados');
  }

  /* ---------- UI / Validación ---------- */

  _bind(form) {
    // Re-enable botones al tocar cualquier campo
    form.addEventListener('input', (ev) => {
      if (!['INPUT','SELECT','TEXTAREA'].includes(ev.target?.tagName)) return;
      form.querySelectorAll('.btn').forEach(b => {
        b.disabled = false;
        b.classList.remove('disabled', 'fv-plugins-submit-button--disabled');
        b.removeAttribute('data-fv-disabled');
        b.removeAttribute('aria-disabled');
      });
    });
  }

  _initValidation(form) {
    const P = window.FormValidation?.plugins || {};
    if (!this.config.fieldsValidation || !window.FormValidation || !P) return;

    const cfg = {
      fields: this.config.fieldsValidation,
      plugins: {
        trigger:     P.Trigger    ? new P.Trigger() : undefined,
        bootstrap5:  P.Bootstrap5 ? new P.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.fv-row',
          messageContainer: (_field, el) => this._messageContainerFor(el),
        }) : undefined,
        autoFocus:   P.AutoFocus  ? new P.AutoFocus() : undefined,
      },
    };
    if (this.config.useSubmitPlugin && P.SubmitButton) {
      cfg.plugins.submitButton = new P.SubmitButton();
    }

    // Limpia instancias previas (si el form fue reemplazado)
    if (this._inst?.destroy) try { this._inst.destroy(); } catch (e) {}

    this._inst = window.FormValidation
      .formValidation(form, cfg)
      .on('core.form.valid', () => {
        if (this._beforeSubmitUI(form)) this._doLivewireSubmit(form);
      })
      .on('core.form.invalid', () => this._restoreUI(form));

    // Arreglo de contenedores de errores en input-group
    queueMicrotask(() => this._fixMisplacedContainers(form));
  }

  reloadValidation() {
    const form = document.querySelector(this.config.formSelector);
    if (!form) return;
    if (this._inst?.destroy) try { this._inst.destroy(); } catch (e) {}
    this._inst = null;
    this._ensureInit(form);
  }

  _messageContainerFor(el) {
    const group = el.closest('.input-group');
    if (group) {
      let host = group.nextElementSibling;
      if (!host || !host.classList?.contains('fv-message')) {
        host = document.createElement('div');
        host.className = 'fv-message invalid-feedback';
        group.insertAdjacentElement('afterend', host);
      }
      return host;
    }
    let host = el.nextElementSibling;
    if (!host || !host.classList?.contains('fv-message')) {
      host = document.createElement('div');
      host.className = 'fv-message invalid-feedback';
      el.insertAdjacentElement('afterend', host);
    }
    return host;
  }

  _fixMisplacedContainers(form) {
    form.querySelectorAll('.input-group .fv-plugins-message-container').forEach(msg => {
      const group = msg.closest('.input-group');
      if (!group) return;
      let host = group.nextElementSibling;
      if (!host || !host.classList?.contains('fv-message')) {
        host = document.createElement('div');
        host.className = 'fv-message invalid-feedback';
        group.insertAdjacentElement('afterend', host);
      }
      host.appendChild(msg);
    });
  }

  _beforeSubmitUI(form) {
    if (this._isSubmitting) return false;
    this._isSubmitting = true;

    // Desactiva todo durante submit
    form.querySelectorAll('.btn').forEach(b => {
      b.disabled = true;
      b.classList.add('disabled');
      b.removeAttribute('data-fv-disabled');
      b.removeAttribute('aria-disabled');
      b.classList.remove('fv-plugins-submit-button--disabled');
    });
    form.querySelectorAll('input,select,textarea,button').forEach(f => f.disabled = true);

    const host = this._currentHostBtn || form.querySelector('.btn-save');
    const loadingText = host?.getAttribute('data-loading-text');
    if (host && loadingText) {
      if (!host.hasAttribute('data-original-text')) {
        host.setAttribute('data-original-text', host.innerHTML);
      }
      host.innerHTML = loadingText;
    }
    return true;
  }

  _restoreUI(form) {
    form.querySelectorAll('.btn').forEach(b => {
      b.disabled = false;
      b.classList.remove('disabled', 'fv-plugins-submit-button--disabled');
      b.removeAttribute('data-fv-disabled');
      b.removeAttribute('aria-disabled');
    });
    form.querySelectorAll('input,select,textarea,button').forEach(f => f.disabled = false);

    const host = form.querySelector('.btn-save');
    if (host?.hasAttribute('data-original-text')) {
      host.innerHTML = host.getAttribute('data-original-text');
    }
  }

  _unlockAfterLivewire() {
    const form = this._formEl || document.querySelector(this.config.formSelector);
    this._isSubmitting = false;
    if (!form) return;

    this._restoreUI(form);

    // Reset del estado de FormValidation para permitir nuevos envíos
    if (this._inst) {
      try {
        this._inst.resetForm(false);
        if (typeof this._inst.setFormStatus === 'function') {
          this._inst.setFormStatus('NotValidated');
        }
      } catch (e) {}
    }
  }

  /* ---------- envío ---------- */

  _doLivewireSubmit(form) {
    // Usa el wire:id capturado al boot / morph
    const id = this._componentId || form.closest('[wire\\:id]')?.getAttribute('wire:id');
    if (id && this.config.dispatchOnSubmit) {
      window.Livewire?.find(id)?.call(this.config.dispatchOnSubmit);
    } else {
      // Si no hay método configurado, no bloqueo la UI
      this._restoreUI(form);
    }
  }
}

if (!window.formCustomListener) window.formCustomListener = FormCustomListener;
