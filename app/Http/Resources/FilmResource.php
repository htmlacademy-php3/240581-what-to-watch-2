<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class FilmResource extends JsonResource
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
            'preview_image' => $this->preview_image,

            'background_image' => $this->background_image,
            'background_color' => $this->background_color,
            'video_link' => $this->video_link,
            'preview_video_link' => $this->preview_video_link,
            'description' => $this->description,
            'rating' => $this->getTotalRating(),
            'scores_count' => $this->getRating(),
            'director' => $this->director,
            'starring' => $this->actors->pluck('name'),
            'run_time' => $this->run_time,
            'genre' => $this->genres->pluck('title'),
            'released' => $this->released,
            'is_favorite' => $this->when(Auth::user(), function () {
                /*
                $favorite = Favorite::where([
                    ['film_id', $this->id],
                    ['user_id', Auth::user()->id],
                ])
                    ->exists();
                if ($favorite) {
                    return true;
                }*/
                if (in_array(Auth::id(), $this->users->pluck('id')->toArray())) {
                    return true;
                }
                /*
                foreach ($this->users->pluck('id') as $element) {
                    if (Auth::id() === $element) {
                        return true;
                    }
                }*/
                return false;
            }),
        ];
    }
}
