<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        // Get All data dari Posts
        $posts = Post::latest()->paginate(5);

        // Return data ke PostResource atau Parsing data ke PostResource
        return new PostResource( true, 'List data Posts', $posts);
    }

    public function store(Request $request)
    {
        // define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        // Check if validasi fails
        if ($validator->fails())
        {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // return response
        return new PostResource( true, 'Data Post Berhasil Di tambahkan', $post);
    }
}
