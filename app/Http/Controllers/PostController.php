<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts= Post::with('category')->paginate(10);

        $notifications = DB::table('notifications')->get();

        return view('admin.post')->with('posts',$posts)
                                 ->with('notifications',$notifications);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('admin.post.create');
    }
    public function store(Request $request){
      dd($request->all());
      $data = $request()->validate([
        'title' => 'required',
        'des' => 'required',
        'category' => 'required',
      ]);

      auth()->user()->posts()->create([
        'title' => $data['title'],
        'des' => $data['des'],
        'category_id' => $data['category'],
      ]);

      return redirect()->back();

    }
    public function show(\App\Post $post)
    {
      return view('admin.post.show',[
        'post' => $post,
      ]);
    }

    public function delete($post)
    {
      
      $post = Post::findOrFail($post);
      if(auth::user()->roles()->first() != null){
      if (Auth()->user()->id == $post->user_id || auth::user()->roles->first()->name == "admin" || auth::user()->username == "Lam Thai Gia Huy") {
        $post->delete();
        return redirect()->back();
      } else{
        abort(403, 'Unauthorized action.');
        return redirect('/')->with('status', 'Not Authorized!');
      }
    }else if(auth::user()->username != "Lam Thai Gia Huy"){
        abort(403, 'Unauthorized action.');
        return redirect('/')->with('status', 'Not Authorized!');
    }else{
         $post->delete();
        return redirect()->back();
    }


    }

    public function edit(\App\Post $post)
    {
      
      if (Auth()->user()->id == $post->user_id) {
        return view('admin.post.edit', compact('post'));
      } else{
        abort(403, 'Unauthorized action.');
        return redirect('/')->with('status', 'Not Authorized!');
      }
    }

    public function update(\App\Post $post)
    {
     
      if (Auth()->user()->id == $post->user_id) {
        $data = request()->validate([
          'title' =>'',
          'des' => '',
        ]);


        $post->title = request('title');
        $post->des = request('des');
        $post->save();




        return redirect("/profile/{$post->user ->id}");
      } else{
        abort(403, 'Unauthorized action.');
        return redirect('/')->with('status', 'Not Authorized!');
      }
    }
 
}
