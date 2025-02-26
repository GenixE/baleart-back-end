<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'reg_number' => $this->reg_number,
            'observation_CA' => $this->observation_CA,
            'observation_ES' => $this->observation_ES,
            'observation_EN' => $this->observation_EN,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'accessType' => $this->accessType,
            'totalScore' => $this->totalScore,
            'countScore' => $this->countScore,
            'address' => [
                'id' => $this->address->id,
                'name' => $this->address->name,
                'municipality' => [
                    'id' => $this->address->municipality->id,
                    'name' => $this->address->municipality->name,
                    'island' => [
                        'id' => $this->address->municipality->island->id,
                        'name' => $this->address->municipality->island->name
                    ]
                ],
                'zone' => [
                    'id' => $this->address->zone->id,
                    'name' => $this->address->zone->name
                ]
            ],
            'space_type' => [
                'id' => $this->spaceType->id,
                'name' => $this->spaceType->name
            ],
            'modalities' => $this->modalities->map(function($modality) {
                return [
                    'id' => $modality->id,
                    'name' => $modality->name
                ];
            }),
            'services' => $this->services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name
                ];
            }),
            'comments' => $this->comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'score' => $comment->score,
                    'status' => $comment->status,
                    'user' => [ // Include user details for the comment
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'lastName' => $comment->user->lastName
                    ],
                    'images' => $comment->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url
                        ];
                    })
                ];
            }),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
