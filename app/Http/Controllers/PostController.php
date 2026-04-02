<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        return view('pages.blog.show', compact('post'));
    }
}
