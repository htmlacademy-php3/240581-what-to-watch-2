<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\FilmPolicy;
use App\Policies\GenrePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Comment::class => CommentPolicy::class,
        Film::class => FilmPolicy::class,
        Genre::class => GenrePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
