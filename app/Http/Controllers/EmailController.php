<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
/**
 * Class EmailController
 * @package App\Http\Controllers
 */

class EmailController extends Controller
{
    /**
     * Display a email inbox of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(){

        $pageTitle = 'Email Inbox';
        $pageDescription = 'Email inbox';

        $email_info = DB::table('email')->get();

        return view('applications.email.index', compact('pageTitle', 'pageDescription', 'email_info'));
    }

    /**
     * Display a email started of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function started(){

        $pageTitle = 'Email Started';
        $pageDescription = 'Email Started';

        $email_info = DB::table('email')->where('star', 1)->get();

        return view('applications.email.started', compact('pageTitle', 'pageDescription', 'email_info'));
    }
    /**
     * Display a email send of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function send(){

        $pageTitle = 'Email Send';
        $pageDescription = 'Email Send';

        $email_info = DB::table('email')->where('status', 'send')->get();

        return view('applications.email.send', compact('pageTitle', 'pageDescription', 'email_info'));
    }
    
    /**
     * Display a read email of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function readEmail(){

        $pageTitle = 'Read Email';
        $pageDescription = 'Read email';

        return view('applications.email.read_email', compact('pageTitle', 'pageDescription'));
    }
}