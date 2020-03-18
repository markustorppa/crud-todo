<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class TaskController extends Controller
{

  public function __construct() {

    $this->middleware('auth');

  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $listid = $request->input('listid');

      $creatorname = DB::select('select creator_name from lists where id = :list_id',['list_id' => $listid])[0]->creator_name;

      if($creatorname == $request->user()->username || $request->user()->usertype == 'admin') {

        $createdtask = DB::insert('insert into list_tasks (list_id, task) values (?,?)', [$listid, $request->input('newtaskcontent')]);

        $latesttaskid = DB::table('list_tasks')->max('id');

        return response([$createdtask,$latesttaskid], Response::HTTP_OK);

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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

      $listid = $request->input('listid');

      $newtaskcontent = $request->input('newtaskcontent');

      $creatorname = DB::select('select creator_name from lists where id = :list_id',['list_id' => $listid])[0]->creator_name;

      if($creatorname==$request->user()->username || $request->user()->usertype == 'admin') {

        $updatedtask = DB::update('update list_tasks set task = :newtaskcontent where id = :taskid',['newtaskcontent' => $newtaskcontent, 'taskid' => $id]);

        return response($updatedtask, Response::HTTP_OK);

      } else {

        return response('action not allowed', Response::HTTP_OK);

      }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

        //Listan id
        $listid = $request->input('listid');

        //Jos listan id on annettu
        if($listid) {

          $creatorname = DB::select('select creator_name from lists where id = :list_id',['list_id' => $listid])[0]->creator_name;

          if($creatorname == $request->user()->username || $request->user()->usertype == 'admin') {

            $removedtask = DB::delete('delete from list_tasks where id = :task_id', ['task_id' => $id]);

            return response($removedtask, Response::HTTP_OK);

          } else {

            return response('action not allowed', Response::HTTP_OK);

          }

          //Jos listan id:tä ei ole annettu
        } else {

          return response('empty request', Response::HTTP_OK);

        }

    }
}
