<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Models\Setting;

/**
 * Servicio para gestionar la configuración global del sistema.
 *
 * Esta clase maneja las configuraciones globales del sistema, incluyendo servicios
 * externos (Facebook, Google), configuración de Vuexy y sistema de correo.
 * Implementa un sistema de caché para optimizar el rendimiento y proporciona
 * valores predeterminados cuando es necesario.
 */
class GlobalSettingsService
{
    /** @var int Tiempo de vida del caché en minutos (60 * 24 * 30 = 30 días) */
    private $cacheTTL = 60 * 24 * 30;

    /**
     * Carga la configuración del sistema desde la base de datos o caché.
     *
     * Gestiona la carga de configuraciones para servicios externos y Vuexy.
     * Si la base de datos no está inicializada, utiliza valores predeterminados.
     *
     * @return void
     */
    public function loadSystemConfig(): void
    {
        try {
            if (!Schema::hasTable('migrations')) {
                // Base de datos no inicializada: usar valores predeterminados
                $config = $this->getDefaultSystemConfig();
            } else {
                // Cargar configuración desde la caché o base de datos
                $config = Cache::remember('global_system_config', $this->cacheTTL, function () {
                    $settings = Setting::withVirtualValue()
                        ->where('key', 'LIKE', 'config.%')
                        ->pluck('value', 'key')
                        ->toArray();

                    return [
                        'servicesFacebook' => $this->buildServiceConfig($settings, 'config.services.facebook.', 'services.facebook'),
                        'servicesGoogle'   => $this->buildServiceConfig($settings, 'config.services.google.', 'services.google'),
                        'vuexy'            => $this->buildVuexyConfig($settings),
                    ];
                });
            }

            // Aplicar configuración al sistema
            Config::set('services.facebook', $config['servicesFacebook']);
            Config::set('services.google', $config['servicesGoogle']);
            Config::set('vuexy', $config['vuexy']);

        } catch (\Exception $e) {
            // Manejo silencioso de errores para evitar interrupciones
            Config::set('services.facebook', config('services.facebook', []));
            Config::set('services.google', config('services.google', []));
            Config::set('vuexy', config('vuexy', []));
        }
    }

    /**
     * Obtiene la configuración predeterminada del sistema.
     *
     * @return array Configuración predeterminada para servicios y Vuexy
     */
    private function getDefaultSystemConfig(): array
    {
        return [
            'servicesFacebook' => config('services.facebook', [
                'client_id' => '',
                'client_secret' => '',
                'redirect' => '',
            ]),
            'servicesGoogle' => config('services.google', [
                'client_id' => '',
                'client_secret' => '',
                'redirect' => '',
            ]),
            'vuexy' => config('vuexy', []),
        ];
    }

    /**
     * Verifica si existe configuración para un bloque específico.
     *
     * @param array $settings Array de configuraciones
     * @param string $blockPrefix Prefijo del bloque a verificar
     * @return bool True si existe configuración para el bloque
     */
    protected function hasBlockConfig(array $settings, string $blockPrefix): bool
    {
        return array_key_exists($blockPrefix, array_filter($settings, fn($key) => str_starts_with($key, $blockPrefix), ARRAY_FILTER_USE_KEY));
    }

    /**
     * Construye la configuración para un servicio específico.
     *
     * @param array $settings Array de configuraciones
     * @param string $blockPrefix Prefijo del bloque de configuración
     * @param string $defaultConfigKey Clave de configuración predeterminada
     * @return array Configuración del servicio
     */
    protected function buildServiceConfig(array $settings, string $blockPrefix, string $defaultConfigKey): array
    {
        if (!$this->hasBlockConfig($settings, $blockPrefix)) {
            return config($defaultConfigKey)?? [];
        }

        return [
            'client_id'     => $settings["{$blockPrefix}client_id"] ?? '',
            'client_secret' => $settings["{$blockPrefix}client_secret"] ?? '',
            'redirect'      => $settings["{$blockPrefix}redirect"] ?? '',
        ];
    }

    /**
     * Construye la configuración de Vuexy.
     *
     * Combina la configuración predeterminada con los valores almacenados
     * en la base de datos y normaliza los campos booleanos.
     *
     * @param array $settings Array de configuraciones
     * @return array Configuración de Vuexy normalizada
     */
    protected function buildVuexyConfig(array $settings): array
    {
        // Configuración predeterminada del sistema
        $defaultVuexyConfig = config('vuexy', []);

        // Convertimos las claves planas a un array multidimensional
        $settingsNested = Arr::undot($settings);

        // Navegamos hasta la parte relevante del array desanidado
        $vuexySettings = $settingsNested['config']['vuexy'] ?? [];

        // Fusionamos la configuración predeterminada con los valores del sistema
        $mergedConfig = array_replace_recursive($defaultVuexyConfig, $vuexySettings);

        // Normalizamos los valores booleanos
        return $this->normalizeBooleanFields($mergedConfig);
    }

    /**
     * Normaliza los campos booleanos en la configuración.
     *
     * @param array $config Configuración a normalizar
     * @return array Configuración con campos booleanos normalizados
     */
    protected function normalizeBooleanFields(array $config): array
    {
        $booleanFields = [
            'myRTLSupport',
            'myRTLMode',
            'hasCustomizer',
            'displayCustomizer',
            'footerFixed',
            'menuFixed',
            'menuCollapsed',
            'showDropdownOnHover',
        ];

        foreach ($booleanFields as $field) {
            if (isset($config['vuexy'][$field])) {
                $config['vuexy'][$field] = (bool) $config['vuexy'][$field];
            }
        }

        return $config;
    }

    /**
     * Limpia la caché de configuración del sistema.
     *
     * @return void
     */
    public static function clearSystemConfigCache(): void
    {
        Cache::forget('global_system_config');
    }

    /**
     * Limpia la configuración de Vuexy de la base de datos y caché.
     *
     * @return void
     */
    public static function clearVuexyConfig(): void
    {
        Setting::where('key', 'LIKE', 'config.vuexy.%')->delete();

        Cache::forget('global_system_config');
    }

    /**
     * Obtiene la configuración del sistema de correo.
     *
     * Recupera y estructura la configuración de correo incluyendo
     * configuración SMTP, direcciones de envío y respuesta.
     *
     * @return array Configuración completa del sistema de correo
     */
    public function getMailSystemConfig(): array
    {
        return Cache::remember('mail_system_config', $this->cacheTTL, function () {
            $settings = Setting::withVirtualValue()
                ->where('key', 'LIKE', 'mail.%')
                ->pluck('value', 'key')
                ->toArray();

            $defaultMailersSmtpVars = config('mail.mailers.smtp');

            return [
                'mailers' => [
                    'smtp' => array_merge($defaultMailersSmtpVars, [
                        'url'        => $settings['mail.mailers.smtp.url'] ?? $defaultMailersSmtpVars['url'],
                        'host'       => $settings['mail.mailers.smtp.host'] ?? $defaultMailersSmtpVars['host'],
                        'port'       => $settings['mail.mailers.smtp.port'] ?? $defaultMailersSmtpVars['port'],
                        'encryption' => $settings['mail.mailers.smtp.encryption'] ?? 'TLS',
                        'username'   => $settings['mail.mailers.smtp.username'] ?? $defaultMailersSmtpVars['username'],
                        'password'   => isset($settings['mail.mailers.smtp.password']) && !empty($settings['mail.mailers.smtp.password'])
                            ? Crypt::decryptString($settings['mail.mailers.smtp.password'])
                            : $defaultMailersSmtpVars['password'],
                        'timeout'    => $settings['mail.mailers.smtp.timeout'] ?? $defaultMailersSmtpVars['timeout'],
                    ]),
                ],
                'from' => [
                    'address' => $settings['mail.from.address'] ?? config('mail.from.address'),
                    'name'    => $settings['mail.from.name'] ?? config('mail.from.name'),
                ],
                'reply_to' => [
                    'method' => $settings['mail.reply_to.method'] ?? config('mail.reply_to.method'),
                    'email'  => $settings['mail.reply_to.email'] ?? config('mail.reply_to.email'),
                    'name'   => $settings['mail.reply_to.name'] ?? config('mail.reply_to.name'),
                ],
            ];
        });
    }

    /**
     * Limpia la caché de configuración del sistema de correo.
     *
     * @return void
     */
    public static function clearMailSystemConfigCache(): void
    {
        Cache::forget('mail_system_config');
    }

}
