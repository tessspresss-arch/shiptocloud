<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $moduleIds = array_column(User::managedModules(), 'id');
        $user = $this->route('utilisateur');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(['admin', 'medecin', 'secretaire'])],
            'password' => ['nullable', 'string', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised(3)],
            'module_permissions' => ['nullable', 'array'],
            'module_permissions.*' => ['string', Rule::in($moduleIds)],
            'professional_phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'speciality' => ['nullable', 'string', 'max:120'],
            'order_number' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'account_status' => ['required', Rule::in(['actif', 'desactive', 'en_attente', 'suspendu'])],
            'account_expires_at' => ['nullable', 'date'],
            'ui_language' => ['required', Rule::in(['fr', 'en', 'ar'])],
            'timezone' => ['required', Rule::in(['Africa/Casablanca', 'Europe/Paris', 'UTC'])],
            'notification_channel' => ['required', Rule::in(['email', 'sms', 'email_sms'])],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'force_password_change' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=2000,max_height=2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $fields = ['first_name', 'name', 'email', 'professional_phone', 'job_title', 'speciality', 'order_number', 'department'];

        $normalized = [];
        foreach ($fields as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                $normalized[$field] = is_string($value) ? trim($value) : $value;
            }
        }

        if (isset($normalized['email']) && is_string($normalized['email'])) {
            $normalized['email'] = mb_strtolower($normalized['email'], 'UTF-8');
        }

        $lastName = trim((string) ($normalized['name'] ?? $this->input('name', '')));
        $firstName = trim((string) ($normalized['first_name'] ?? $this->input('first_name', '')));
        $normalized['name'] = trim($lastName . ' ' . $firstName);

        if ($this->has('account_status')) {
            $status = mb_strtolower(trim((string) $this->input('account_status')), 'UTF-8');
            $normalized['account_status'] = $status === 'suspendu' ? 'desactive' : $status;
        }

        $this->merge($normalized);
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'prenom',
            'name' => 'nom',
            'email' => 'adresse email',
            'role' => 'role',
            'password' => 'mot de passe',
            'password_confirmation' => 'confirmation du mot de passe',
            'module_permissions' => 'modules autorises',
            'professional_phone' => 'telephone professionnel',
            'job_title' => 'fonction',
            'speciality' => 'specialite',
            'order_number' => "numero d'ordre",
            'department' => 'service',
            'account_status' => 'statut du compte',
            'account_expires_at' => 'date d expiration du compte',
            'ui_language' => 'langue',
            'timezone' => 'fuseau horaire',
            'notification_channel' => 'mode de notification',
            'two_factor_enabled' => 'double authentification',
            'force_password_change' => 'changement de mot de passe force',
            'avatar' => 'avatar',
        ];
    }
}
