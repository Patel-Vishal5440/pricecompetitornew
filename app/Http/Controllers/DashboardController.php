<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */

class DashboardController extends Controller
{
    /**
     * Display a dashboard social media of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(){

        $pageTitle = 'Social Media Dashboard';
        $pageDescription = 'Some description for the page';

        $facebook_overviews_today = DB::table('facebook_overviews')->where('status', 'today')->first();
        $facebook_overviews_week = DB::table('facebook_overviews')->where('status', 'week')->first();
        $facebook_overviews_month = DB::table('facebook_overviews')->where('status', 'month')->first();
        $facebook_overviews_year = DB::table('facebook_overviews')->where('status', 'year')->first();

        $twitter_overviews_week = DB::table('twitter_overviews')->where('status', 'week')->first();
        $twitter_overviews_month = DB::table('twitter_overviews')->where('status', 'month')->first();
        $twitter_overviews_year = DB::table('twitter_overviews')->where('status', 'year')->first();

        $social_traffics_metrics_today = DB::table('social_traffics_metrics')->where('status', 'today')->get();
        $social_traffics_metrics_week = DB::table('social_traffics_metrics')->where('status', 'week')->get();
        $social_traffics_metrics_month = DB::table('social_traffics_metrics')->where('status', 'month')->get();
        $social_traffics_metrics_year = DB::table('social_traffics_metrics')->where('status', 'year')->get();

        $youtube_subscribers_week = DB::table('youtube_subscribers')->where('status', 'week')->first();
        $youtube_subscribers_month = DB::table('youtube_subscribers')->where('status', 'month')->first();
        $youtube_subscribers_year = DB::table('youtube_subscribers')->where('status', 'year')->first();

        return view('dashboard.social_media', 
        compact(
            'pageTitle', 'pageDescription', 
            'facebook_overviews_today', 'facebook_overviews_week', 'facebook_overviews_month', 'facebook_overviews_year',
            'twitter_overviews_week', 'twitter_overviews_month', 'twitter_overviews_year',
            'social_traffics_metrics_today', 'social_traffics_metrics_week', 'social_traffics_metrics_month', 'social_traffics_metrics_year',
            'youtube_subscribers_week', 'youtube_subscribers_month', 'youtube_subscribers_year'
        ));
    }

    /**
     * Display a dashboard business of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function business(){

        $pageTitle = 'FineTech / Business Dashboard';
        $pageDescription = 'Some description for the page';

        return view('dashboard.business', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Display a dashboard performance of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function performance(){

        $pageTitle = 'Website Performance Dashboard';
        $pageDescription = 'Some description for the page';

        return view('dashboard.performance', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Display a dashboard ecommerce of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function ecommerce(){

        $pageTitle = 'Ecommerce Dashboard';
        $pageDescription = 'Some description for the page';

        return view('dashboard.ecommerce', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Display a dashboard crm of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function crm(){

        $pageTitle = 'CRM Dashboard';
        $pageDescription = 'Some description for the page';

        return view('dashboard.crm', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Display a dashboard sales performance of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function sales(){

        $pageTitle = 'Sales Performance Dashboard';
        $pageDescription = 'Some description for the page';

        return view('dashboard.sales', compact('pageTitle', 'pageDescription'));
    }
}