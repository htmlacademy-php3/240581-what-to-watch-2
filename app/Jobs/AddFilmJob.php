<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FilmService;

class AddFilmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  string $imdbId - Действительный идентификатор IMDb в The Open Movie Database (например, tt1285016)
     * @param  MovieRepositoryInterface $repository - Репозиторий для класса Film
     *
     * @return void
     */
    public function __construct(private string $imdbId, private $repository = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->repository) {
            $service = new FilmService($this->repository);
        } else {
            $service = new FilmService();
        }

        $filmData = $service->searchFilm($this->imdbId);
        $service->saveFilm($filmData);
    }
}
