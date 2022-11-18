<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Action::where('user_id', Auth::id())->get();
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
        //
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
    public function update(Request $request)
    {
        $actionData = $request->input('money');
        // return $actionData['date'];
        $amount = $actionData['amount'];
        $action = Action::where('id', $actionData['id'])->first();
        if (!$action) {
            $action = new Action();
            $action->id = $actionData['id'];
            $action->user_id = Auth::id();
            $action->description = $actionData['description'];
            $action->type = $actionData['actiontype'];
            $action->amount = $amount;
            $action->date = $actionData['date'];

            $balance = User::where('id', Auth::id())->first();
            if ($actionData['actiontype'] == 1) {

                $balance->balance += (float) $amount;
            } else {
                $balance->balance -= (float) $amount;
            }
            $balance->save();
            
        } else {
            $action->description = $actionData['description'];
            $action->type = $actionData['actiontype'];
            $diffamount = $amount - $action->amount ;
            $action->amount = $amount;
            $action->date = $actionData['date'];

            $balance = User::where('id', Auth::id())->first();
            if ($actionData['actiontype'] == 0) {

                $balance->balance -= (float) $diffamount;
            } else {
                $balance->balance += (float) $diffamount;
            }
            $balance->save();
            
        }
        $action->save();

        return response([
            'message' => 'Updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $action = Action::find($id)->first();
        $balance = User::where('id', Auth::id())->first();

        if($action->type == 1){
            $balance->balance -= $action->amount;
        }
        else{
            
            $balance->balance += $action->amount;
        }
        $balance->save();
        $action->delete();
    }
}
