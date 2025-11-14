<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

/**
 * Class UserController
 * @package App\Http\Controllers
 */

class UserController extends Controller
{
    /**
     * Display a team of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  index(Request $request){

        $pageTitle = 'Team';
        $pageDescription = 'Some description for the page';

        if($request->search){
            $user_teams = DB::table('user_teams')
            ->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('position', 'like', '%'.$request->search.'%')
            ->get();
            }else{
                $user_teams = DB::table('user_teams')->get();
            }
        return view('users.team', compact('pageTitle', 'pageDescription', 'user_teams'));
    }

    /**
     * Display a users of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  users(Request $request){

        $pageTitle = 'Users';
        $pageDescription = 'Some description for the page';

        if($request->search){
            $users_grid_style = DB::table('users_grid_style')
            ->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('position', 'like', '%'.$request->search.'%')
            ->paginate(12);
        }else{
            $users_grid_style = DB::table('users_grid_style')->paginate(12);
        }

        return view('users.users', compact('pageTitle', 'pageDescription', 'users_grid_style'));
    }

    /**
     * Display a users grid of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  usersGrid(Request $request){

        $pageTitle = 'Users Grid';
        $pageDescription = 'Some description for the page';
        if($request->search){
            $users_grid = DB::table('users_grid')
            ->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('position', 'like', '%'.$request->search.'%')
            ->get();
        }else{
            $users_grid = DB::table('users_grid')->get();
        }
        return view('users.users_grid', compact('pageTitle', 'pageDescription', 'users_grid'));
    }
    
    /**
     * Display a users grid of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  addUser(){

        $pageTitle = 'Add User';
        $pageDescription = 'Some description for the page';

        return view('users.add_user', compact('pageTitle', 'pageDescription'));
    }

    /**
     * Display a users list of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  usersList(Request $request){

        $pageTitle = 'Users List';
        $pageDescription = 'Some description for the page';
        if($request->search){
            $users_list = DB::table('users_list')
            ->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('position', 'like', '%'.$request->search.'%')
            ->orWhere('aboutus', 'like', '%'.$request->search.'%')
            ->orWhere('per_hour', 'like', '%'.$request->search.'%')
            ->orWhere('earned', 'like', '%'.$request->search.'%')
            ->paginate(12);
        }else{
            $users_list = DB::table('users_list')->paginate(12);
        }
        return view('users.users_list', compact('pageTitle', 'pageDescription', 'users_list'));
    }

    /**
     * Display a users group of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  usersGroup(Request $request){

        $pageTitle = 'Users Group';
        $pageDescription = 'Some description for the page';
        
        if($request->search){
            $users_group = DB::table('users_group')
            ->where('title', 'like', '%'.$request->search.'%')
            ->orWhere('location', 'like', '%'.$request->search.'%')
            ->orWhere('body', 'like', '%'.$request->search.'%')
            ->orWhere('current_project', 'like', '%'.$request->search.'%')
            ->paginate(12);
        }else{
            $users_group = DB::table('users_group')->paginate(12);
        }


        return view('users.users_group', compact('pageTitle', 'pageDescription', 'users_group'));
    }

    /**
     * Display a users datatable of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function  usersDatatable(Request $request){

        $pageTitle = 'Users data table';
        $pageDescription = 'Some description for the page';
        if($request->search){
            $users_list = DB::table('users_list_datatable')
            ->where('user', 'like', '%'.$request->search.'%')
            ->orWhere('location', 'like', '%'.$request->search.'%')
            ->orWhere('email', 'like', '%'.$request->search.'%')
            ->orWhere('company', 'like', '%'.$request->search.'%')
            ->orWhere('position', 'like', '%'.$request->search.'%')
            ->paginate(12);
        }else{
            $users_list = DB::table('users_list_datatable')->paginate(12);
        }
        return view('users.users_datatable', compact('pageTitle', 'pageDescription', 'users_list'));
    }
}