<?php

namespace App\Livewire;

use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CommentItem extends Component
{

    use WithFileUploads;
    public Comment $comment;
    public $hasLiked = false;
    public $likesCount = 0;

    public $showReplies = false;
    public $newReply = '';
    public $repliesCount = 0;

    public $replyMedia;

    public function mount(Comment $comment){
        $this->comment=$comment;
        $this->hasLiked = $comment->likes->contains('user_id', auth()->id());
        $this->likesCount = $comment->likes->count();
        $this->repliesCount = $comment->comments()->count();
    }

    public function toggleLike(){
        $user_id = auth()->id();
        $exists = $this->comment->likes()->where('user_id', $user_id)->first();

        if($exists){
            $exists->delete();
            $this->hasLiked = false;
            $this->likesCount--;
        } else {
            $this->comment->likes()->create(['user_id' => $user_id]);
            $this->hasLiked = true;
            $this->likesCount++;
        }
    }

    public function toggleReplies(){
        $this->showReplies = !$this->showReplies;
    }

    public function addReply(){
        $this->validate([
            'newReply' => 'required|string|max:280',
            'replyMedia' => 'nullable|image|max:10240'
        ]);

        $path = null;

        if($this->replyMedia){
            $path = $this->replyMedia->store('social_comments', 'public');
        }

        

        $this->comment->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newReply,
            'media_path' => $path
        ]);
        $this->reset(['newReply', 'replyMedia']);
        $this->repliesCount++;
        $this->comment->refresh();
    }

    public function deleteComment(){
        if(auth()->id() === $this->comment->user_id){
            if($this->comment->media_path){
                Storage::disk('public')->delete($this->comment->media_path);
            }

            $this->comment->delete();
            $this->dispatch('post-deleted');
        }
    }

    public function render()
    {
        return view('livewire.comment-item');
    }
}
