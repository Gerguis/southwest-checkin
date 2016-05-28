<?php
namespace App\Http\Controllers;

use App\Jobs\CheckInJob;
use App\SouthwestRequest;
use App\User;
use Carbon\Carbon;

class IndexController extends Controller
{


    public function __construct(SouthwestRequest $request)
    {
        $this->request = $request;
    }

    public function getIndex()
    {
        $this->dispatch(new CheckInJob());
    }


}