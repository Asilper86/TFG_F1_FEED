<?php

namespace App\Livewire;

use App\Models\Social_post;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
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

    public $hasReposted = false;

    public function mount(Social_post $post)
    {
        $this->post = $post;

        $this->isFollowing = auth()->user()->following()->where('followed_id', $post->user_id)->exists();
        $this->hasLiked = $post->likes->contains('user_id', auth()->id());
        $this->likesCount = $post->likes->count();
        $this->commentsCount = $post->comments()->count();
        $this->hasReposted = Social_post::where('user_id', auth()->id())
            ->where('original_post_id', $this->post->id)
            ->exists();

        $targetUserId = $post->original_post_id ? $post->originalPost->user_id : $post->user_id;
        $this->isFollowing = auth()->user()->following()->where('followed_id', $targetUserId)->exists();
    }

    public function toggleLike()
    {
        $user_id = auth()->id();
        $exists = $this->post->likes()->where('user_id', $user_id)->first();

        if ($exists) {
            $exists->delete();
            $this->hasLiked = false;
            $this->likesCount--;
        } else {
            $this->post->likes()->create(['user_id' => $user_id]);
            $this->hasLiked = true;
            $this->likesCount++;
        }
    }

    public function toggleFollow()
    {
        $targetUserId = $this->post->original_post_id
        ? $this->post->originalPost->user_id
        : $this->post->user_id;
        if (auth()->id() !== $targetUserId) {
            auth()->user()->following()->toggle($targetUserId);
            $this->isFollowing = ! $this->isFollowing;
            $this->dispatch('follow-updated');
        }
    }

    public function toggleComments()
    {
        $this->showComments = ! $this->showComments;
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:280',
            'commentMedia' => 'nullable|image|max:10240',
        ]);

        $path = null;
        if ($this->commentMedia) {
            $path = $this->commentMedia->store('social_comments', 'public');
        }
        $this->post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment,
            'media_path' => $path,
        ]);

        $this->reset(['newComment', 'commentMedia']);
        $this->commentsCount++;
        $this->post->refresh();
    }

    public function deleteComment($commentId)
    {
        $comment = $this->post->comments()->find($commentId);
        if ($comment && auth()->id() === $comment->user_id) {
            if ($comment->media_path) {
                Storage::disk('public')->delete($comment->media_path);
            }
            $comment->delete();
            $this->commentsCount--;
            $this->post->refresh();
        }
    }

    #[On('follow-updated')]
    public function updateFollowState()
    {
        $targetUserId = $this->post->original_post_id 
        ? $this->post->originalPost->user_id 
        : $this->post->user_id;
        $this->isFollowing = auth()->user()->following()->where('followed_id', $targetUserId)->exists();
    }

    public function deletePost()
    {
        if (auth()->id() === $this->post->user_id) {
            if ($this->post->media_path) {
                Storage::disk('public')->delete($this->post->media_path);
            }

            $this->post->delete();
            $this->dispatch('post-deleted');
        }
    }

    public function repost()
    {
        $existe = Social_post::where('user_id', auth()->id())
            ->where('original_post_id', $this->post->id)
            ->first();

        if ($existe) {
            $existe->delete();
            $this->hasReposted = false;
        } else {
            Social_post::create([
                'user_id' => auth()->id(),
                'original_post_id' => $this->post->id,
            ]);
            $this->hasReposted = true;

        }
    }

    public function render()
    {
        return view('livewire.post-item');
    }
}
