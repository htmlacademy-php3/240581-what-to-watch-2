<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class SimilarController extends Controller
{
    /**
     * Получение списка похожих фильмов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getResponse(Test::test());
    }
}
