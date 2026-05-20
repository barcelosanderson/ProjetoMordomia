<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relacionamento: um usuário pode ter várias tarefas.
     */
    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class);
    }

    /**
     * Relacionamento: um usuário pode ter vários itens de compra.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Relacionamento: um usuário pode ter vários compromissos.
     */
    public function compromissos(): HasMany
    {
        return $this->hasMany(Compromisso::class);
    }

    /**
     * Relacionamento: um usuário pode ter várias transações financeiras.
     */
    public function transacoes(): HasMany
    {
        return $this->hasMany(Transacao::class);
    }

    /**
     * Conversões automáticas de campos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
