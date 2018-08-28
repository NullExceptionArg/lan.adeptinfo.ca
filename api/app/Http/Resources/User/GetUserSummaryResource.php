<?php

namespace App\Http\Resources\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Resources\Json\Resource;

class GetUserSummaryResource extends Resource
{
    protected $requestCount;

    public function __construct(Authenticatable $resource, int $requestCount)
    {
        $this->requestCount = $requestCount;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'request_count' => intval($this->requestCount)
        ];
    }
}
