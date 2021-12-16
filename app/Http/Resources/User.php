<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (string)$this->id,
            'userId' => (string)$this->user_id,
            'name' => (string)$this->name,
            'phoneNumber' => (string)$this->phone_number,
            'email' => (string)$this->email,
            'active' => (bool)$this->active,
            'ipAddress' => (string)$this->ip,
            'device' => (string)$this->device,
            'last_login' => Carbon::parse($this->last_login)->format('Y-m-d H:m:s'),
            'joined' => Carbon::parse($this->created_at)->format('Y-m-d H:m:s'),
        ];
    }
}