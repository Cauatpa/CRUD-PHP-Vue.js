<?php

namespace Src\Controllers;

use Src\Models\User;
use Src\Validation\UserValidator;
use Src\Repositories\UserRepository;

class UserController
{
    public function __construct(private UserRepository $repo = new UserRepository()) {}

    public function list(): array
    {
        $users = array_map(fn(User $u) => $u->toArray(), $this->repo->findAll());
        return [
            'message' => ['error' => false],
            'users'   => $users,
        ];
    }

    public function create(array $payload): array
    {
        $user = User::fromArray($payload);

        [$ok, $msg] = UserValidator::validate($user);
        if (!$ok) {
            return ['message' => ['error' => true, 'msgError' => $msg]];
        }

        if ($this->repo->emailExists($user->email)) {
            return ['message' => ['error' => true, 'msgError' => 'E-mail já cadastrado.']];
        }

        $newId = $this->repo->create($user);
        return ['message' => ['error' => false, 'msgSucces' => 'Usuário criado com sucesso.'], 'id' => $newId];
    }

    public function update(array $payload): array
    {
        $user = User::fromArray($payload);
        if (!$user->id) {
            return ['message' => ['error' => true, 'msgError' => 'ID é obrigatório para atualizar.']];
        }

        [$ok, $msg] = UserValidator::validate($user);
        if (!$ok) {
            return ['message' => ['error' => true, 'msgError' => $msg]];
        }

        if ($this->repo->emailExists($user->email, $user->id)) {
            return ['message' => ['error' => true, 'msgError' => 'E-mail já cadastrado para outro usuário.']];
        }

        $this->repo->update($user);
        return ['message' => ['error' => false, 'msgSucces' => 'Usuário atualizado com sucesso.']];
    }

    public function delete(array $payload): array
    {
        $id = isset($payload['id']) ? (int)$payload['id'] : 0;
        if ($id <= 0) {
            return ['message' => ['error' => true, 'msgError' => 'ID inválido.']];
        }

        $this->repo->delete($id);
        return ['message' => ['error' => false, 'msgSucces' => 'Usuário excluído com sucesso.']];
    }
}
