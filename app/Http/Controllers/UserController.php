<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rules\File;

use App\Models\User;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Mail; 
use App\Mail\NotifyMail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource(users).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(5);

        return view('users.index',compact('users'))

            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage(database).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'photo' => [
                'required',
                File::image()
                    ->min(1)
                    ->max(12 * 1024)
                    /*->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500))*/,
            ],
        ]);      

        $params = $request->all();
        $name = $request->file('photo')->getClientOriginalName(); 
        $path = $request->file('photo')->store('public/photos');

        User::create([
            'name'=> $params['name'],
            'phone'=> $params['phone'],
            'email'=> $params['email'],
            'photo'=> $name

        ]); 

        return redirect()->route('users.index')
                        ->with('success','User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
           
        ]);

        $params = $request->all();
        $user->update([
            'name'=> $params['name'],
            'phone'=> $params['phone'],
            'email'=> $params['email'],

        ]);
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete(); 

        Mail::to($user->email)->send(new NotifyMail());
 
        if (Mail::failures()) {
           return response()->Fail('Sorry! Please try again latter');
        }else{
           return response()->success('Great! Successfully send in your mail');
        }

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

        return redirect()->route('users.index')
                        ->with('success','user deleted successfully');
    }

    public function sendSms(User $user)
    {
        return view('users.sendsms',compact('user'));
    }
}
