<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Koneko\VuexyAdmin\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings_array = [
            /*
            'app_title' => 'Quimiplastic S.A de C.V.',
            'app_faviconIcon' => '../assets/img/logo/koneko-02.png',
            'app_name' => 'Quimiplastic',
            'app_imageLogo' => '../assets/img/logo/koneko-02.png',

            'app_myLayout' => 'vertical',
            'app_myTheme' => 'theme-default',
            'app_myStyle' => 'light',
            'app_navbarType' => 'sticky',
            'app_menuFixed' => true,
            'app_menuCollapsed' => false,
            'app_headerType' => 'static',
            'app_showDropdownOnHover' => false,
            'app_authViewMode' => 'cover',
            'app_maxQuickLinks' => 5,



            'smtp.host' => 'webmail.koneko.mx',
            'smtp.port' => 465,
            'smtp.encryption' => 'tls',
            'smtp.username' => 'no-responder@koneko.mx',
            'smtp.password' => null,
            'smtp.from_email' => 'no-responder@koneko.mx',
            'smtp.from_name' => 'Koneko Soluciones en Tecnología',
            'smtp.reply_to_method' => 'smtp',
            'smtp.reply_to_email' => null,
            'smtp.reply_to_name' => null,



            'website.title',
            'website.favicon',
            'website.description',
            'website.image_logo',
            'website.image_logoDark',

            'admin.title',
            'admin.favicon',
            'admin.description',
            'admin.image_logo',
            'admin.image_logoDark',


            'favicon.icon' => null,

            'contact.phone_number' => '(222) 462 0903',
            'contact.phone_number_ext' => 'Ext. 5',
            'contact.email' => 'virtualcompras@live.com.mx',
            'contact.form.email' => 'contacto@conciergetravellife.com',
            'contact.form.email_cc' => 'arturo@koneko.mx',
            'contact.form.subject' => 'Has recibido un mensaje del formulario de covirsast.com',
            'contact.direccion' => '51 PTE 505 loc. 14, Puebla, Pue.',
            'contact.horario' => '9am - 7 pm',
            'contact.location.lat' => '19.024439',
            'contact.location.lng' => '-98.215777',

            'social.whatsapp' => '',
            'social.whatsapp.message' => '👋 Hola! Estoy buscando más información sobre Covirsa Soluciones en Tecnología. ¿Podrías proporcionarme los detalles que necesito? ¡Te lo agradecería mucho! 💻✨',

            'social.facebook' => 'https://www.facebook.com/covirsast/?locale=es_LA',
            'social.Whatsapp' => '2228 200 201',
            'social.Whatsapp.message' => '¡Hola! 🌟 Estoy interesado en obtener más información acerca de Concierge Travel. ¿Podrías ayudarme con los detalles? ¡Gracias de antemano! ✈️🏝',
            'social.Facebook' => 'test',
            'social.Instagram' => 'test',
            'social.Linkedin' => 'test',
            'social.Tiktok' => 'test',
            'social.X_twitter' => 'test',
            'social.Google' => 'test',
            'social.Pinterest' => 'test',
            'social.Youtube' => 'test',
            'social.Vimeo' => 'test',


            'chat.provider' => '',
            'chat.whatsapp.number' => '',
            'chat.whatsapp.message' => '👋 Hola! Estoy buscando más información sobre Covirsa Soluciones en Tecnología. ¿Podrías proporcionarme los detalles que necesito? ¡Te lo agradecería mucho! 💻✨',

            'webTpl.container' => 'custom-container',
            */
        ];

        foreach ($settings_array as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        };
    }
}
