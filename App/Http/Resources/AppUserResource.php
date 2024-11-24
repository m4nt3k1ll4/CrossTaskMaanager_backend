<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AppUserResource extends JsonResource
{
    public function toArray($request)
    {
        $authenticatedUser = Auth::user();
        $includePassword = $authenticatedUser && $authenticatedUser->isCeo();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $includePassword ? $this->password : null,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->role),
            'headquarter' => $this->headquarter,

        ];
    }
}

