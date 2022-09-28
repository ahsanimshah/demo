<?php

namespace App\Http\Controllers;

use App\Models\smss;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
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
        return view('sms.sendsms');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sms_text' => 'required',
            'user_id' => 'required'
            
        ]);

        $params = $request->all();

        User::create([
            'sms_text'=> $params['sms_text'],
            'user_id'=> $params['user_id'],              

        ]);


        $receiverNumber = "+923216804814";

        $message = $params['sms_text'];  

        try {  

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");  

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message]
            );  

            dd('SMS Sent Successfully.');

  

        } catch (Exception $e) {

            dd("Error: ". $e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\smss  $smss
     * @return \Illuminate\Http\Response
     */
    public function show(smss $smss)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\smss  $smss
     * @return \Illuminate\Http\Response
     */
    public function edit(smss $smss)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\smss  $smss
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, smss $smss)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\smss  $smss
     * @return \Illuminate\Http\Response
     */
    public function destroy(smss $smss)
    {
        //
    }
}
