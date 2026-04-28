<?php

namespace App\Livewire;

use App\Models\Hashtag;
use App\Models\Social_post;
use App\Models\User;
use Livewire\Component;

class SearchGlobal extends Component
{
    public $searchQuery = '';
    public $filter = 'all';


    public function render()
    {
       $users = [];
       $posts = [];
       $hashtags = [];

        if(strlen($this->searchQuery) >= 2){
            if($this->filter === 'all' || $this->filter === 'users'){
                $users = User::where('name', 'like', '%'.$this->searchQuery.'%')
                ->orWhere('username', 'like', '%'.$this->searchQuery.'%')
                ->limit(10)->get();
            }

            if($this->filter === 'all' || $this->filter === 'posts'){
                $posts = Social_post::with(
                    [
                        'user', 'likes', 'lap.session', 'comments.user'
                    ])
                    ->where('content', 'like', '%'.$this->searchQuery.'%')
                    ->latest()->limit(20)->get();
            }

            if($this->filter==='all' || $this->filter === 'hashtags'){
                $hashtags = Hashtag::where('name', 'like', '%'.$this->searchQuery.'%')
                    ->withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->limit(10)->get();
            }
        }

        return view('livewire.search-global', [
            'users' => $users,
            'posts' => $posts,
            'hashtags' => $hashtags
        ])->layout('layouts.app');
    }

    public function deletePost($postId)
    {
        $post = Social_post::find($postId);
        if ($post && auth()->id() === $post->user_id) {
            if ($post->media_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->media_path);
            }
            $post->delete();
        }
    }
}
