<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
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

    public function show($id)
    {
        // find post by ID
        $post = Post::find($id);

        // return single post as a resource
        return new PostResource( true, 'Detail Data Post', $post);
    }

    public function update(Request $request, $id)
    {
        // Define Validation Rules
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        // Check if validasi fails
        if ($validator->fails())
        {
            return response()->json($validator->errors(), 422);
        }

        // find post by ID
        $post = Post::find($id);

        // check if image is not empty
        if ($request->hasFile('image'))
        {
            // upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // delete old image
            Storage::delete('public/posts/' . basename($post->image));

            // update with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);


        } else {

            // update without new image
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

        }
        // return response
        return new PostResource( true, 'Data Post Berhasil Di Update', $post);
    }

    public function destroy($id)
    {
        // find post by ID
        $post = Post::find($id);

        // delete image
        Storage::delete('public/posts/' . basename($post->image));

        // delete post
        $post->delete();

        // return response
        return new PostResource( true, 'Data Post Berhasil Di Hapus', null);
    }
}
