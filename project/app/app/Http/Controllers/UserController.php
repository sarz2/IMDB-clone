<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Movie;
use App\Models\Image;



use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function register(Request $request)
    {
        // dd('at least I have made it this far');
        $attributes = request()->validate([
            'name' => 'required', //['required', 'string', 'min:5', 'max:255'],
            'email' => 'required', //['required', 'max:255', 'email', 'unique:users,email'],
            'password' => 'required' // ['required', 'string', 'confirmed', 'min:7', 'max:255'] // 'confirmed' for user regis
            //'is_admin' => ['required']
        ]);

        //dd('validation success'); //for debugging, to see if store method is called
        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => bcrypt($attributes['password'])
            // is_admin is set to false by default 
        ]);

        $token = $user->createToken('apptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        session()->flash('success', 'User registered', response($response, 201));
        return response($response, 201);
        //return redirect('home');
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        session()->flash('success', 'User logged out');
        return ['message' => 'User logged out'];
        //return redirect('home');
    }

    //login with email and password
    public function login(Request $request)
    {

        $attributes = request()->validate([
            //'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'] // 'confirmed' for user regis
        ]);

        // get (first) user with matching email, will be null if user does not exist
        $user = User::where('email', $attributes['email'])->first();
        // return error if user does NOT exist or psw does NOT match
        if (!$user || !Hash::check($attributes['password'], $user->password)) {
            return response(['message' => 'user does not exist or password is incorrect'], 401);
        }
        // else assign token
        $token = $user->createToken('apptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        session()->flash('success', 'User logged in',  response($response, 201));
        // return response($response, 201); //if api, if called from wep.php use:
        return redirect('home'); //or whatever landig page is called, nneds to be defined
    }


    // public function index()
    // {
    //     //show all unsers
    //     //$users = User::latest()->firstWhere(request(['name', 'email', 'password','watchlist']))->paginate(2)->withQueryString(); //lookup eager loading
    //     //$users = User::firstWhere(request(['name', 'email', 'password','watchlist']))->paginate(3)->withQueryString(); //
    //     $users = User::latest()->paginate(5);
    //     //$users = User::latest()->get();
    //     //$users = User::all();  // same as 'get()' ?
    //     return view('admin.admin-main', ['users' => $users]); //->paginate(2);      
    //     // return view('admin.admin-main',compact('users'))
    //     // ->with('i', (request()->input('page', 1) - 1) * 5);
    // }

   
    // public function create()
    // {
    //     //
    //     // need to implement authorization logic here
    //     if (auth()->user()->is_admin === false) {   //pseudocode! require middleware 'auth'
    //         abort(Response::HTTP_FORBIDDEN);
    //     }



    //     return view('users.create'); //route needs to be defined
    // }

    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->is_admin) :
            //dd(request());
            $request["is_admin"] = $request["is_admin"] ? 1 : 0; //convert checkbox value to tinyint, 1 or 0
            $attributes = request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:255'],
                'email' => ['required', 'max:255', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:7', 'max:255'], // 'confirmed' for user regis
                'is_admin' => ['required']
            ]);

            //dd('validation success'); //for debugging, to see if store method is called
            //User::create($attributes);
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => bcrypt($attributes['password']),
                'is_admin' => $attributes['is_admin']
            ]);

            session()->flash('success', 'User created successfully');
        //return redirect()->back(); //back() redirects to previous page
        endif;
        return redirect()->back();
    }

    
    public function edit($id)
    {
        $user = User::find($id);  
        //dd($user);
        return view('admin.edit-user', ['user' => $user]);

    }

    
    public function search(Request $request) // and/ or $name
    {
        
        $query = $request->input('query');
        //dd($query);
        $user = User::where('email', 'like', '%' . $query . '%')->orWhere('name', 'like', '%' . $query . '%')->first(); // '%' are regex placeholders, 

        // grouped orWhere clause should be used instead according to docs but throws error
        // $user = User::where (function ($query) 
        // {$query->where('email', 'like', '%' . $query . '%')
        //     ->orWhere('name', 'like', '%' . $query . '%');})
        // ->first(); 

        return view('admin.edit-user', ['user' => $user]);
    }

    
    public function update(Request $request, $id)
    {
        //dd($request["is_admin"]);
        $user = User::find($id);
        $request["is_admin"] = $request["is_admin"] ? 1 : 0; 
        $user->update($request->all());
        
        return redirect('admin-main')->with('status','User data updated.');
        //return redirect()->back();
       
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        //
        $userID = Auth::user('id');
        $user = User::find($userID)->first();

        // ---------------DOESNT WORK
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
        ]);

        if ($validation->fails()) {
            $errors = response()->json([
                'status' => 'failure',
                'errors' => $validation->errors()
            ], 400);

            session()->flash('status', $errors);

            return redirect('user/user-settings');
        }

        // $user::update([
        //     'name' => $updates['name'],
        //     'email' => $updates['email'],
        //     'password' => Hash::make($updates['password'])
        // ]);


        // ------------WORKS BUT IS PROBABLY BAD PRACTICE
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        

        if(($request->get('password')) === ($request->get('passConfirm'))){
            $user->password = password_hash($request->get('password'), PASSWORD_BCRYPT); 
        }
        $user->save();

        session()->flash('success', 'updated successfully!');

        return redirect('user/user-settings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        User::destroy($id);
        session()->flash('success', 'User deleted');
        //return redirect()->back(); //back() redirects to previous page
        return redirect()->back();
    }

    // public function search($email) // and/ or $name
    // {
    //     //
    //     return User::where('email', 'like', '%' . $email . '%')->get(); // '%' are regex placeholders, 
    //     // for exact search, do
    //     //return User::where('email', $email)->get();

    // }

    public function settings()
    {
        $user = User::get();
        $image = Image::where('user_id', Auth::user()->id)->first();

        return view('user-settings', ['image' => $image]);
    }

    public function updateWatchlist($movieId) 
    {
        $user = User::find(Auth::user()->id);

        $watchlist = [];

        if ($user->watchlist != null) {
            $watchlist = json_decode($user->watchlist);
        }
        
        array_push($watchlist, $movieId);

        $user->watchlist = json_encode($watchlist);
        $user->update();
        return redirect()->back()->with('status', 'Movie added to watchlist');

    }

    public function removeFromWatchlist($movieId)
    {
        $user = User::find(Auth::user()->id);

        $watchlist = [];
        foreach (json_decode($user->watchlist) as $value) {
            if ($value != $movieId) {
                array_push($watchlist, $value);
            }
        }

        $user->watchlist = json_encode($watchlist);
        $user->update();
        return redirect()->back()->with('status', 'Movie removed from watchlist');
    }

    public function showWatchlist(){
        $user = User::find(Auth::user()->id);
        $image = Image::where('user_id', $user)->first();


        $watchlistMovies= [];

            foreach(json_decode($user->watchlist) as $id){
                $movie = Movie::where('id', $id)->first();
                $imgsToArray = json_decode($movie->urls_images); 

                $watchlistMovie = [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'genre' => $movie->genre,
                    'cast' => json_decode($movie->cast),
                    'abstract' => $movie->abstract,
                    'urls_images' => "https://image.tmdb.org/t/p/w1280$imgsToArray[0]",
                    'url_trailer' => $movie->url_trailer,
                    'avg_rating' => $movie->avg_rating,
                    'released' => $movie->released
                ];
                array_push($watchlistMovies, $watchlistMovie);
            }
         return view('/userpage', ['watchlist' => $watchlistMovies, 'image' => $image]);


    }
}
