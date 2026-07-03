<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    /*public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }*/

    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.required' => __('Por favor ingresa tu contraseña actual.'),
            'current_password.current_password' => __('La contraseña actual no coincide con nuestros registros.'),
            'password.required' => __('La nueva contraseña es obligatoria.'),
            'password.min' => __('La nueva contraseña debe tener al menos 8 caracteres.'),
            'password.confirmed' => __('Las contraseñas no coinciden.'),
        ])->validateWithBag('updatePassword');
    
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
