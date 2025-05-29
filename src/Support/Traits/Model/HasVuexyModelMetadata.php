<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Model;

use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Support\Macros\StrMacros;

StrMacros::register();

/**
 * Proporciona metadatos legibles y consistentes para modelos Vuexy.
 */
trait HasVuexyModelMetadata
{
    /**
     * Devuelve el tagName del modelo (snake-case o definido manualmente).
     */
    public function getTagName(): string
    {
        return $this->tagName ?? class_basename($this);
    }

    /**
     * Devuelve el nombre de la columna por defecto para ordenar.
     */
    public function getSortColumn(): string
    {
        return $this->sortColumn ?? 'id';
    }

    /**
     * Devuelve el ordenamiento por defecto (asc o desc).
     */
    public function getDefaultSortOrder(): string
    {
        return $this->defaultSortOrder ?? 'asc';
    }


    /**
     * Devuelve el nombre singular legible.
     */
    public function getSingularName(): string
    {
        return $this->singularName ?? Str::of(class_basename($this))->snake()->replace('_', ' ')->lower()->toString();
    }

    /**
     * Devuelve el nombre plural legible.
     */
    public function getPluralName(): string
    {
        return $this->pluralName ?? Str::pluralEsPhrase($this->getSingularName());
    }

    /**
     * Devuelve el nombre de la columna a la que se debe enfocarse al abrir el formulario.
     */
    public function getFocusColumnOnOpen(): mixed
    {
        return $this->focusColumnOnOpen ?? false;
    }

    public function getDisplayName(): string
    {
        return $this->{$this->focusColumnOnOpen};
    }
}
