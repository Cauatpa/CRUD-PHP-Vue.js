<?php

namespace Src\Validation;

use Src\Models\User;

class UserValidator
{
    public static function validate(User $user): array
    {
        if ($user->name === '') {
            return [false, 'Nome é obrigatório.'];
        }
        if (mb_strlen($user->name) > 100) {
            return [false, 'Nome deve ter no máximo 100 caracteres.'];
        }

        if ($user->email === '') {
            return [false, 'E-mail é obrigatório.'];
        }
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'E-mail inválido.'];
        }
        if (mb_strlen($user->email) > 150) {
            return [false, 'E-mail deve ter no máximo 150 caracteres.'];
        }

        if ($user->phone === '') {
            return [false, 'Telefone é obrigatório.'];
        }
        if (mb_strlen($user->phone) > 30) {
            return [false, 'Telefone deve ter no máximo 30 caracteres.'];
        }

        return [true, null];
    }
}
