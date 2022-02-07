<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Http\Controllers\API\BaseController as BaseController;

class LevelController extends BaseController
{
    public function index() {
        $dataLevel = Level::all();
        return $this->sendResponse($dataLevel, 'Data Level fetched.');
    }
}
