<?php

namespace App\Http\Resources\Contribution;

use App\Model\User;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @property int id
 * @property string user_full_name
 * @property int user_id
 */
class ContributionResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $userFullName = $this->user_full_name != null ?
            $this->user_full_name :
            User::find($this->user_id)->getFullName();
        return [
            'id' => $this->id,
            'user_full_name' => $userFullName
        ];
    }
}
