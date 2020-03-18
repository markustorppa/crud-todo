<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class ListController extends Controller
{

  public function __construct() {

    $this->middleware('auth');

  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      //Jos käyttäjä on admin, niin silloin näkyviin tulevat kaikkien käyttäjien tehtävälistat
      if($request->user()->usertype == 'admin') {

        $lists = DB::select('select * from lists');

        //Jos käyttäjän tyyppi on "normal", niin haetaan listat käyttäjänimen perusteella tietokannasta
      } elseif ($request->user()->usertype == 'normal') {

        $lists = DB::select('select * from lists where creator_name = :username', ['username' => $request->user()->username]);

      }

      return response($lists, Response::HTTP_OK);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

      //Haetaan listan tehneen käyttäjän nimi tehtävälistan id:n perusteella
      $creatorname = DB::select('select creator_name from lists where id = :list_id',['list_id' => $id])[0]->creator_name;

      if($creatorname == $request->user()->username || $request->user()->usertype == 'admin') {

        $removedlist = DB::delete('delete from lists where id = :list_id', ['list_id' => $id]);

        $removedtasks = DB::delete('delete from list_tasks where list_id = :list_id', ['list_id' => $id]);

        return response([$removedlist,$removedtasks], Response::HTTP_OK);

      } else {

        return response('action not allowed', Response::HTTP_OK);

      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {

        //Haetaan käyttäjän nimi tehtävälistan id:n perusteella
        $creatorname = DB::select('select creator_name from lists where id = :list_id',['list_id' => $id])[0]->creator_name;

        //Jos sisäänkirjautunut käyttäjä on tehnyt tehtävälistan, tai käyttäjän tyyppi on "admin"
        if($creatorname == $request->user()->username || $request->user()->usertype == 'admin') {

          //Näytetään valitun listan tehtävät
          $tasks = DB::select('select * from list_tasks where list_id = :list_id', ['list_id' => $id]);

          return response($tasks, Response::HTTP_OK);

        } else {

          return response('action not allowed', Response::HTTP_OK);

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      //Luodaan uusi tehtävälista käyttäjän tiedoilla
      $createdlist = DB::insert('insert into lists (list_name, creator_name) values (?,?)', [$request->input('listname'), $request->user()->username]);

      //Haetaan lisätyn listan id
      $latestlistid = DB::table('lists')->max('id');

      return response([$createdlist, $latestlistid], Response::HTTP_OK);

    }

}
