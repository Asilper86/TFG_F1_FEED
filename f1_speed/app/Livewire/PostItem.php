<?php

namespace App\Livewire;

use App\Models\Social_post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class PostItem extends Component
{
    use WithFileUploads;
    public Social_post $post;
    
    public $commentMedia;
    public $isFollowing = false;
    public $hasLiked = false;
    public $likesCount = 0;
    public $showComments = false;
    public $newComment = '';
    public $commentsCount = 0;

    public function mount(Social_post $post){
        $this->post = $post;
        
        $this->isFollowing = auth()->user()->following()->where('followed_id', $post->user_id)->exists();
        $this->hasLiked = $post->likes->contains('user_id', auth()->id());
        $this->likesCount = $post->likes->count();
        $this->commentsCount = $post->comments()->count();
    }

    public function toggleLike(){
        $user_id = auth()->id();
        $exists = $this->post->likes()->where('user_id', $user_id)->first();

        if($exists){
            $exists->delete();
            $this->hasLiked = false;
            $this->likesCount--;
        } else {
            $this->post->likes()->create(['user_id' => $user_id]);
            $this->hasLiked = true;
            $this->likesCount++;
        }
    }

    public function toggleFollow(){
        if(auth()->id() !== $this->post->user_id){
            auth()->user()->following()->toggle($this->post->user_id);
            
            $this->isFollowing = !$this->isFollowing;
            $this->dispatch('follow-updated');
        }
    }

    public function toggleComments(){
        $this->showComments = !$this->showComments;
    }

    public function addComment(){
        $this->validate([
            'newComment' => 'required|string|max:280',
            'commentMedia' => 'nullable|image|max:10240'
        ]);

        $path = null;
        if($this->commentMedia){
            $path = $this->commentMedia->store('social_comments', 'public');
        }
        $this->post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment,
            'media_path' => $path
        ]);

        $this->reset(['newComment', 'commentMedia']);
        $this->commentsCount++;
        $this->post->refresh();
    }

    public function deleteComment($commentId){
        $comment = $this->post->comments()->find($commentId);
        if ($comment && auth()->id() === $comment->user_id) {
            if ($comment->media_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($comment->media_path);
            }
            $comment->delete();
            $this->commentsCount--;
            $this->post->refresh();
        }
    }

    #[On('follow-updated')]
    public function updateFollowState()
    {
        $this->isFollowing = auth()->user()->following()->where('followed_id', $this->post->user_id)->exists();
    }

    public function deletePost(){
        if(auth()->id() === $this->post->user_id){
            if($this->post->media_path){
                Storage::disk('public')->delete($this->post->media_path);
            }

            $this->post->delete();
            $this->dispatch('post-deleted');
        }
    }

    public function render()
    {
        return view('livewire.post-item');
    }
}
