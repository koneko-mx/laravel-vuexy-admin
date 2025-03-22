<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use App\Models\User;
use App\Models\Catalog\DropdownList;
use Koneko\SatCatalogs\Models\UsoCfdi;
use Koneko\SatCatalogs\Models\RegimenFiscal;
use Intervention\Image\ImageManager;
use Livewire\WithFileUploads;
use Livewire\Component;

class UserShow extends Component
{
    use WithFileUploads;

    public $image;

    public User $user;
    public $status_options, $pricelists_options;
    public $regimen_fiscal_options, $uso_cfdi_options;
    public $userId,
        $name,
        $cargo,
        $profile_photo,
        $profile_photo_path,
        $email,
        $password,
        $password_confirmation,
        $tipo_persona,
        $rfc,
        $nombre_fiscal,
        $c_regimen_fiscal,
        $domicilio_fiscal,
        $c_uso_cfdi,
        $pricelist_id,
        $enable_credit,
        $credit_days,
        $credit_limit,
        $is_prospect,
        $is_customer,
        $is_provider,
        $status;
    public $deleteUserImage;
    public $cuentaUsuarioAlert,
        $accesosAlert,
        $facturacionElectronicaAlert;

    // Reglas de validación para la cuenta de usuario
    protected $rulesUser = [
        'tipo_persona' => 'nullable|integer',
        'name' => 'required|string|min:3|max:255',
        'cargo' => 'nullable|string|min:3|max:255',
        'is_prospect' => 'nullable|boolean',
        'is_customer' => 'nullable|boolean',
        'is_provider' => 'nullable|boolean',
        'pricelist_id' => 'nullable|integer',
        'enable_credit' => 'nullable|boolean',
        'credit_days' => 'nullable|integer',
        'credit_limit' => 'nullable|numeric|min:0|max:9999999.99|regex:/^\d{1,7}(\.\d{1,2})?$/',
        'image' => 'nullable|mimes:jpg,png|image|max:20480', // 20MB Max
    ];

    // Reglas de validación para los campos fiscales
    protected $rulesFacturacion = [
        'rfc' => 'nullable|string|max:13',
        'domicilio_fiscal' => [
            'nullable',
            'regex:/^[0-9]{5}$/',
            'exists:sat_codigo_postal,c_codigo_postal'
        ],
        'nombre_fiscal' => 'nullable|string|max:255',
        'c_regimen_fiscal' => 'nullable|integer',
        'c_uso_cfdi' => 'nullable|string',
    ];

    public function mount($userId)
    {
        $this->user = User::findOrFail($userId);

        $this->reloadUserData();

        $this->pricelists_options = DropdownList::selectList(DropdownList::POS_PRICELIST);

        $this->status_options = [
            User::STATUS_ENABLED  => User::$statusList[User::STATUS_ENABLED],
            User::STATUS_DISABLED => User::$statusList[User::STATUS_DISABLED],
        ];

        $this->regimen_fiscal_options = RegimenFiscal::selectList();
        $this->uso_cfdi_options = UsoCfdi::selectList();
    }


    public function reloadUserData()
    {
        $this->tipo_persona  = $this->user->tipo_persona;
        $this->name          = $this->user->name;
        $this->cargo         = $this->user->cargo;
        $this->is_prospect   = $this->user->is_prospect? true : false;
        $this->is_customer   = $this->user->is_customer? true : false;
        $this->is_provider   = $this->user->is_provider? true : false;
        $this->pricelist_id  = $this->user->pricelist_id;
        $this->enable_credit = $this->user->enable_credit? true : false;
        $this->credit_days   = $this->user->credit_days;
        $this->credit_limit  = $this->user->credit_limit;
        $this->profile_photo = $this->user->profile_photo_url;
        $this->profile_photo_path = $this->user->profile_photo_path;
        $this->image = null;
        $this->deleteUserImage = false;

        $this->status   = $this->user->status;
        $this->email    = $this->user->email;
        $this->password = null;
        $this->password_confirmation = null;

        $this->rfc              = $this->user->rfc;
        $this->domicilio_fiscal = $this->user->domicilio_fiscal;
        $this->nombre_fiscal    = $this->user->nombre_fiscal;
        $this->c_regimen_fiscal = $this->user->c_regimen_fiscal;
        $this->c_uso_cfdi       = $this->user->c_uso_cfdi;

        $this->cuentaUsuarioAlert = null;
        $this->accesosAlert       = null;
        $this->facturacionElectronicaAlert = null;
    }


    public function saveCuentaUsuario()
    {
        try {
            // Validar Información de usuario
            $validatedData = $this->validate($this->rulesUser);

            $validatedData['name']          = trim($validatedData['name']);
            $validatedData['cargo']         = $validatedData['cargo']? trim($validatedData['cargo']): null;
            $validatedData['is_prospect']   = $validatedData['is_prospect'] ? 1 : 0;
            $validatedData['is_customer']   = $validatedData['is_customer'] ? 1 : 0;
            $validatedData['is_provider']   = $validatedData['is_provider'] ? 1 : 0;
            $validatedData['pricelist_id']  = $validatedData['pricelist_id'] ?: null;
            $validatedData['enable_credit'] = $validatedData['enable_credit'] ? 1 : 0;
            $validatedData['credit_days']   = $validatedData['credit_days'] ?: null;
            $validatedData['credit_limit']  = $validatedData['credit_limit'] ?: null;

            if($this->tipo_persona == User::TIPO_RFC_PUBLICO){
                $validatedData['cargo']         = null;
                $validatedData['is_prospect']   = null;
                $validatedData['is_provider']   = null;
                $validatedData['enable_credit'] = null;
                $validatedData['credit_days']   = null;
                $validatedData['credit_limit']  = null;
            }

            if(!$this->user->is_prospect && !$this->user->is_customer){
                $validatedData['pricelist_id'] = null;
            }

            if(!$this->user->is_customer){
                $validatedData['enable_credit'] = null;
                $validatedData['credit_days']   = null;
                $validatedData['credit_limit']  = null;
            }

            $this->user->update($validatedData);


            if($this->deleteUserImage && $this->user->profile_photo_path){
                $this->user->deleteProfilePhoto();

                // Reiniciar variables después de la eliminación
                $this->deleteUserImage    = false;
                $this->profile_photo_path = null;
                $this->profile_photo      = $this->user->profile_photo_url;

            }else if ($this->image) {
                $image = ImageManager::imagick()->read($this->image->getRealPath());
                $image = $image->scale(520, 520);

                $imageName = $this->image->hashName(); // Genera un nombre único

                $image->save(storage_path('app/public/profile-photos/' . $imageName));

                $this->user->deleteProfilePhoto();

                $this->profile_photo_path = $this->user->profile_photo_path = 'profile-photos/' . $imageName;
                $this->profile_photo      = $this->user->profile_photo_url;
                $this->user->save();

                unlink($this->image->getRealPath());

                $this->reset('image');
            }

            // Puedes también devolver un mensaje de éxito si lo deseas
            $this->setAlert('Se guardó los cambios exitosamente.', 'cuentaUsuarioAlert');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si hay errores de validación, los puedes capturar y manejar aquí
            $this->setAlert('Ocurrieron errores en la validación: ' . $e->validator->errors()->first(), 'cuentaUsuarioAlert', 'danger');
        }
    }

    public function saveAccesos()
    {
        try {
            $validatedData = $this->validate([
                'status' => 'integer',
                'email' => ['required', 'email', 'unique:users,email,' . $this->user->id],
                'password' => ['nullable', 'string', 'min:6', 'max:32', 'confirmed'], // La regla 'confirmed' valida que ambas contraseñas coincidan
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'Debes ingresar un correo electrónico válido.',
                'email.unique' => 'Este correo ya está en uso.',
                'password.min' => 'La contraseña debe tener al menos 5 caracteres.',
                'password.max' => 'La contraseña no puede tener más de 32 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]);

            // Si la validación es exitosa, continuar con el procesamiento
            $validatedData['email'] = trim($this->email);

            if ($this->password)
                $validatedData['password'] = bcrypt($this->password);

            else
                unset($validatedData['password']);

            $this->user->update($validatedData);

            $this->password = null;
            $this->password_confirmation = null;

            // Puedes también devolver un mensaje de éxito si lo deseas
            $this->setAlert('Se guardó los cambios exitosamente.', 'accesosAlert');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si hay errores de validación, los puedes capturar y manejar aquí
            $this->setAlert('Ocurrieron errores en la validación: ' . $e->validator->errors()->first(), 'accesosAlert', 'danger');
        }
    }

    public function saveFacturacionElectronica()
    {
        try {
                // Validar Información fiscal
            $validatedData = $this->validate($this->rulesFacturacion);

            $validatedData['rfc']              = strtoupper(trim($validatedData['rfc'])) ?: null;
            $validatedData['domicilio_fiscal'] = $validatedData['domicilio_fiscal'] ?: null;
            $validatedData['nombre_fiscal']    = strtoupper(trim($validatedData['nombre_fiscal'])) ?: null;
            $validatedData['c_regimen_fiscal'] = $validatedData['c_regimen_fiscal'] ?: null;
            $validatedData['c_uso_cfdi']       = $validatedData['c_uso_cfdi'] ?: null;

            $this->user->update($validatedData);

            // Puedes también devolver un mensaje de éxito si lo deseas
            $this->setAlert('Se guardó los cambios exitosamente.', 'facturacionElectronicaAlert');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si hay errores de validación, los puedes capturar y manejar aquí
            $this->setAlert('Ocurrieron errores en la validación: ' . $e->validator->errors()->first(), 'facturacionElectronicaAlert', 'danger');
        }
    }


    private function setAlert($message, $alertName, $type = 'success')
    {
        $this->$alertName = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function render()
    {
        return view('livewire.admin.crm.contact-view');
    }
}
