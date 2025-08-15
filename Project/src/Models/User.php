<?php

namespace Src\Models;

class User
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $phone;

    public function __construct(?int $id, string $name, string $email, string $phone)
    {
        $this->id    = $id;
        $this->name  = trim($name);
        $this->email = trim($email);
        $this->phone = trim($phone);
    }

    public static function fromArray(array $data): self
    {
        $id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        return new self(
            $id,
            $data['name']  ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
