# 🧱 Cómo crear un nuevo Seeder compatible

## 1. Crear el Seeder

```php
class ProductoSeeder extends AbstractDataSeeder
{
    use HasSeederFactorySupport;
    use HandlesFileSeeders;

    public function run(array $config = []): void
    {
        $this->seedFromJson('productos.json');
    }

    public function runFake(int $total, array $config = []): void
    {
        Producto::factory()->count($total)->create();
    }
}
```

## 2. Registrar en `config/seeder.php`

```php
'products' => [
  'enabled' => true,
  'seeder' => ProductoSeeder::class,
  'file' => 'products.json',
  'faker_only' => false,
  'fake' => ['min' => 5, 'max' => 100],
],
```