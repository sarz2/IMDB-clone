<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Movie;
use App\Models\Review;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    // get all users and movies
    function showUsersAndMovies()
    {
        $users =  User::orderBy('name')->paginate(5); 
        $movies = Movie::orderBy('title')->paginate(5);
        $reviews = Review::orderBy('title')->paginate(5);
        $usercount = Review::count();
        $moviecount = Movie::count();
        $reviewcount = Review::count();
        $allUsers = User::pluck('id', 'name')->all();
        $allMovies = Movie::pluck('id', 'title')->all();
        //dd($allMovies);
       // dd(compact('users',  'movies'));
        return view('admin.admin-main', compact('users',  'movies', 'reviews', 'usercount', 'moviecount', 'reviewcount', 'allUsers', 'allMovies')); 
    }

    // public function showImage()
    // {
    //     return view('image');
    // }
 
    // public function saveImage(Request $request)
    // {
         
    //     $validatedData = $request->validate([
    //      'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
 
    //     ]);
 
    //     $name = $request->file('image')->getClientOriginalName();
 
    //     $path = $request->file('image')->store('public/images');
 
 
    //     $save = new Image;
 
    //     $save->name = $name;
    //     $save->path = $path;
 
    //     $save->save();
 
    //     return redirect('upload-image')->with('status', 'Image Has been uploaded');
 
    // }
}

