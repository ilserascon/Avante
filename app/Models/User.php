<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'borrado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getRoleNombre(): ?string
    {
        return $this->role?->nombre;
    }

    public function esAdministrador(): bool
    {
        return $this->getRoleNombre() === 'Administrador';
    }

    public function esCotizador(): bool
    {
        return $this->getRoleNombre() === 'Cotizador';
    }

    public function puedeEditarCotizacion(): bool
    {
        return $this->esAdministrador() || $this->esCotizador();
    }

    public function puedeGestionarEstatusCotizacion(): bool
    {
        return $this->esAdministrador();
    }

    public function veUtilidadCotizacion(): bool
    {
        return $this->esAdministrador();
    }

    public function veTotalesCotizacion(): bool
    {
        return $this->esAdministrador() || $this->esCotizador();
    }

    public function veDetalleTelaManoObra(): bool
    {
        return $this->esAdministrador();
    }

    public function veCostosCotizacion(): bool
    {
        return $this->esAdministrador();
    }

    /** Costo/utilidad en insumos y precio interno en productos. */
    public function vePreciosInternosCatalogo(): bool
    {
        return $this->veCostosCotizacion();
    }

    public function puedeVerPdfDecorador(): bool
    {
        return $this->esAdministrador() || $this->esCotizador();
    }
}
