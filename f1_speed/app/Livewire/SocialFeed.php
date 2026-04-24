<?php

namespace App\Livewire;

use App\Models\Social_post;
use Livewire\Attributes\On;
use Livewire\Component;

class SocialFeed extends Component
{

    
    public $feedType = 'global'; 
    public function setFeedType($type)
    {
        $this->feedType = $type;
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

    #[On(['post-created', 'follow-updated'])]
    public function render()
    {
        $query = Social_post::with(['user', 'likes'])->latest();
        if ($this->feedType === 'following') {
            $followedIds = auth()->user()->following()->pluck('users.id');
            $followedIds->push(auth()->id()); 
            $query->whereIn('user_id', $followedIds);
        }
        $posts = $query->get();
        return view('livewire.social-feed', compact('posts'));
    }
}
