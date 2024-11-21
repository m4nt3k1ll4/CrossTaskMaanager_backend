<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppUserResource extends JsonResource
{
    public function toArray($request)
    {
        $includePassword = $this->id === 1;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->role),
            'password' => $includePassword ? $this->password : null, 
        ];
    }
}

