<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'poster_image' => $this->poster_image,
            'preview_video_link' => $this->preview_video_link,
        ];
    }
}
