<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index()
    {
        // Get All data dari Posts
        $posts = Post::latest()->paginate(5);

        // Return data ke PostResource atau Parsing data ke PostResource
        return new PostResource( true, 'List data Posts', $posts);
    }
}
