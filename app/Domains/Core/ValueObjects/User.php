<?php

namespace App\Domains\Core\ValueObjects;

use App\Domains\Core\Models\User as ModelsUser;

final class User
{
  private ModelsUser $user;

  public function __construct(private readonly string $value)
  {
    $this->validate();
    $this->user = $this->findUser();
  }

  public function getUser(): ModelsUser
  {
    return $this->user;
  }

  public function getId(): string
  {
    return $this->user->id;
  }

  public function __toString(): string
  {
    return $this->getId();
  }

  public static function fromString(string $value): self
  {
    return new self($value);
  }

  public function validate(): void
  {
    if(empty(trim($this->value))) {
      throw new \InvalidArgumentException('User identifier cannot be empty.');
    }
  }

  private function findUser(): ModelsUser
  {
    try {
      return ModelsUser::findOrFail($this->value);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      throw new \InvalidArgumentException("User with ID {$this->value} not found.", 0, $e);
    }
  }


}
