<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\services\CommentService;

class ExternalComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loading new external comments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $commentService = new CommentService();
        $allComments = $commentService->getAllNewComments();
        $setComments = $commentService->attachNewCommentToFilm($allComments);
    }
}
