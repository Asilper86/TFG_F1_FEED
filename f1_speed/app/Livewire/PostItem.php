<?php

namespace App\Livewire;

use App\Models\F1Notification;
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
    
    protected $targetPost;

    public function boot()
    {
        $this->targetPost = $this->post->original_post_id ? $this->post->originalPost : $this->post;
    }

    public function mount(Social_post $post)
    {
        $this->post = $post;
        $this->targetPost = $post->original_post_id ? $post->originalPost : $post;

        $this->hasLiked = $this->targetPost->likes->contains('user_id', auth()->id());
        $this->likesCount = $this->targetPost->likes->count();
        $this->commentsCount = $this->targetPost->comments()->count();

        $this->hasReposted = Social_post::where('user_id', auth()->id())
            ->where('original_post_id', $this->targetPost->id)
            ->exists();

        $this->isFollowing = auth()->user()->following()->where('followed_id', $this->targetPost->user_id)->exists();
    }

    public function toggleLike()
    {
        $user_id = auth()->id();
        $exists = $this->targetPost->likes()->where('user_id', $user_id)->first();
        
        if ($exists) {
            $exists->delete();
            $this->hasLiked = false;
            $this->likesCount--;
        } else {
            $this->targetPost->likes()->create(['user_id' => $user_id]);
            $this->hasLiked = true;
            $this->likesCount++;
            
            if ($user_id !== $this->targetPost->user_id) {
                F1Notification::create([
                    'user_id' => $this->targetPost->user_id,
                    'actor_id' => $user_id,
                    'type' => 'like',
                    'notifiable_id' => $this->targetPost->id,
                    'notifiable_type' => Social_post::class,
                ]);
            }
        }
    }

    public function toggleFollow()
    {
        $targetUserId = $this->targetPost->user_id;
        if (auth()->id() !== $targetUserId) {
            $wasFollowing = $this->isFollowing;
            auth()->user()->following()->toggle($targetUserId);
            $this->isFollowing = ! $this->isFollowing;
            $this->dispatch('follow-updated');
            if (! $wasFollowing) {
                F1Notification::create([
                    'user_id' => $targetUserId,
                    'actor_id' => auth()->id(),
                    'type' => 'follow',
                ]);
            }
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
        
        $this->targetPost->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment,
            'media_path' => $path,
        ]);

        $this->reset(['newComment', 'commentMedia']);
        $this->commentsCount++;
        $this->targetPost->refresh();

        if (auth()->id() !== $this->targetPost->user_id) {
            F1Notification::create([
                'user_id'         => $this->targetPost->user_id,
                'actor_id'        => auth()->id(),
                'type'            => 'comment',
                'notifiable_id'   => $this->targetPost->id,
                'notifiable_type' => Social_post::class,
            ]);
        }
    }

    public function deleteComment($commentId)
    {
        $comment = $this->targetPost->comments()->find($commentId);
        if ($comment && auth()->id() === $comment->user_id) {
            if ($comment->media_path) {
                Storage::disk('public')->delete($comment->media_path);
            }
            $comment->delete();
            $this->commentsCount--;
            $this->targetPost->refresh();
        }
    }

    #[On('follow-updated')]
    public function updateFollowState()
    {
        $this->isFollowing = auth()->user()->following()->where('followed_id', $this->targetPost->user_id)->exists();
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
            ->where('original_post_id', $this->targetPost->id)
            ->first();
            
        if ($existe) {
            $existe->delete();
            $this->hasReposted = false;
        } else {
            Social_post::create([
                'user_id' => auth()->id(),
                'original_post_id' => $this->targetPost->id,
            ]);
            $this->hasReposted = true;
            
            if (auth()->id() !== $this->targetPost->user_id) {
                F1Notification::create([
                    'user_id' => $this->targetPost->user_id,
                    'actor_id' => auth()->id(),
                    'type' => 'repost',
                    'notifiable_id' => $this->targetPost->id,
                    'notifiable_type' => Social_post::class,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.post-item');
    }
}
