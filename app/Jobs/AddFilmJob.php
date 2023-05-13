<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use App\Services\FilmService;

class AddFilmJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  string $imdbId - Действительный идентификатор IMDb в The Open Movie Database (например, tt1285016)
     *
     * @return void
     */
    public function __construct(private string $imdbId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $filmService = App::make(FilmService::class);

        $filmData = $filmService->searchFilm($this->imdbId);
        $filmService->saveFilm($filmData);
    }
}
