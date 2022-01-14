<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function view()
    {
        // get current logged in user
        $user = Auth::user();
        // load post
        $post = Post::find(1);
        // var_dump($post->user_id);
        // var_dump($user->id);
        if ($user->can('view', $post)) {
            echo "Current logged in user is allowed to update the Post: {$post->id}";
        } else {
            echo 'Not Authorized.';
        }
    }

    public function create()
    {
        // get current logged in user
        $user = Auth::user();
        if ($user->can('create', Post::class)) {
            echo 'Current logged in user is allowed to create new posts.';
        } else {
            echo 'Not Authorized';
        }
        exit;
    }

    public function update()
    {
        // get current logged in user
        $user = Auth::user();

        // load post
        $post = Post::find(1);

        if ($user->can('update', $post)) {
            echo "Current logged in user is allowed to update the Post: {$post->id}";
        } else {
            echo 'Not Authorized.';
        }
    }

    public function delete()
    {
        // get current logged in user
        $user = Auth::user();

        // load post
        $post = Post::find(1);

        if ($user->can('delete', $post)) {
            echo "Current logged in user is allowed to delete the Post: {$post->id}";
        } else {
            echo 'Not Authorized.';
        }
    }
}
