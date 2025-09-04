<?php

namespace Modules\Admin\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar_url,
            'status' => $this->status,
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'last_login_at' => $this->last_login_at?->toISOString(),
            'last_login_ip' => $this->last_login_ip,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'two_factor_enabled' => $this->two_factor_enabled,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
