# 🏭 Koneko ERP - Factory Design Guide

Este documento describe cómo crear y extender `Factories` para modelos en el ecosistema de Koneko ERP.

## 🎯 Objetivo

Facilitar la generación de datos de prueba y semilla utilizando una estructura clara, coherente y extensible para todos los modelos del sistema.

## 🧱 Clase Base: `AbstractModelFactory`

```php
namespace Koneko\VuexyAdmin\Support\Factories;

abstract class AbstractModelFactory extends Factory
{
    // Inyecta Faker automáticamente
    // Ofrece métodos auxiliares como maybe()
}
```

## 🧬 Traits útiles

- `HasFactorySupport`: `maybe($probabilidad, $valor)`
- `HasContactFakeData`: CURP, RFC, teléfono
- `HasFactoryEnumSupport`: Soporte aleatorio de Enums
- `HasDynamicFactoryExtenders`: para métodos como `definitionXyz()`

## 🚘 Ejemplo: Factory para `Vehicle`

Ver archivo: `VehicleFactory.php`
