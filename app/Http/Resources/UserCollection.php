<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'total_users' => $this->total(),
            'count' => $this->count(),
            'links' => [
                'next_url' => $this->nextPageUrl(),
                'prev_url' => $this->previousPageUrl(),
            ],
            'users' => $this->collection->map(function ($user) {
                return [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'position' => $user->position,
                    'position_id' => (string) $user->position_id,
                    'registration_timestamp' => $user->registration_timestamp,
                    'photo' => $user->photo,
                ];
            }),
        ];
    }

    /**
     * Customize the response to not wrap the data key (disable default wrapping).
     *
     * @return \Illuminate\Http\Response
     */
    public function withResponse($request, $response)
    {
        $response->setData($this->toArray($request));
    }
}
