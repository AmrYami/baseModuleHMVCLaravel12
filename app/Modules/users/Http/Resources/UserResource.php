<?php

namespace Users\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $permissions = [];
        if (isset($this->roles[0]) && isset($this->roles[0]->permissions))
            foreach ($this->roles[0]->permissions as $permission) {
                $permissions[] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            "status" => (int) $this->status,
            "code" => $this->code,
            "type" => $this->type,
            "language" => "en",
            "banned_until" => $this->banned_until,
            "freeze" => $this->freeze,
            'role_id' => $this->roles[0]->id ?? null,
            'role_name' => $this->roles[0]->name ?? null,
            'permissions' => $permissions,
        ];
    }
}
