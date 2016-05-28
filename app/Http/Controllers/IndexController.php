<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CheckInJob;
use App\SouthwestRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('home');
    }

    public function postAccount(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:accounts,username',
            'password' => 'required',
        ]);

        $account = Auth::user()->accounts()->create($request->all());

        return redirect('/');
    }


}