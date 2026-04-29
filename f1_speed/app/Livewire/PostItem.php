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

    // ID del post "real" (si es repost, el original; si no, el propio)
    public $targetPostId;

    private function getTarget(): Social_post
    {
        if ($this->post->original_post_id) {
            return $this->post->originalPost ?? $this->post;
        }
        return $this->post;
    }

    public function mount(Social_post $post)
    {
        $this->post = $post;
        $target = $this->getTarget();
        $this->targetPostId = $target->id;

        $this->hasLiked = $target->likes->contains('user_id', auth()->id());
        $this->likesCount = $target->likes->count();
        $this->commentsCount = $target->comments()->count();

        $this->hasReposted = Social_post::where('user_id', auth()->id())
            ->where('original_post_id', $target->id)
            ->exists();

        $this->isFollowing = auth()->user()->following()
            ->where('followed_id', $target->user_id)
            ->exists();
    }

    public function deletePost()
    {
        // Recargamos el post fresco de la BD para evitar problemas de estado
        $post = Social_post::find($this->post->id);

        if (!$post) return;

        // Comprobamos que el usuario autenticado es el dueño
        if ((int) auth()->id() !== (int) $post->user_id) {
            return;
        }

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();
        $this->dispatch('post-deleted');
    }

    public function toggleLike()
    {
        $target = Social_post::findOrFail($this->targetPostId);
        $user_id = auth()->id();
        $exists = $target->likes()->where('user_id', $user_id)->first();

        if ($exists) {
            $exists->delete();
            $this->hasLiked = false;
            $this->likesCount--;
        } else {
            $target->likes()->create(['user_id' => $user_id]);
            $this->hasLiked = true;
            $this->likesCount++;

            if ($user_id !== $target->user_id) {
                F1Notification::create([
                    'user_id' => $target->user_id,
                    'actor_id' => $user_id,
                    'type' => 'like',
                    'notifiable_id' => $target->id,
                    'notifiable_type' => Social_post::class,
                ]);
            }
        }
    }

    public function toggleFollow()
    {
        $target = Social_post::findOrFail($this->targetPostId);
        $targetUserId = $target->user_id;

        if (auth()->id() !== $targetUserId) {
            $wasFollowing = $this->isFollowing;
            auth()->user()->following()->toggle($targetUserId);
            $this->isFollowing = !$this->isFollowing;
            $this->dispatch('follow-updated');

            if (!$wasFollowing) {
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
        $this->showComments = !$this->showComments;
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:280',
            'commentMedia' => 'nullable|image|max:10240',
        ]);

        $target = Social_post::findOrFail($this->targetPostId);

        $path = null;
        if ($this->commentMedia) {
            $path = $this->commentMedia->store('social_comments', 'public');
        }

        $target->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment,
            'media_path' => $path,
        ]);

        $this->reset(['newComment', 'commentMedia']);
        $this->commentsCount++;
        $target->refresh();

        if (auth()->id() !== $target->user_id) {
            F1Notification::create([
                'user_id' => $target->user_id,
                'actor_id' => auth()->id(),
                'type' => 'comment',
                'notifiable_id' => $target->id,
                'notifiable_type' => Social_post::class,
            ]);
        }
    }

    public function deleteComment($commentId)
    {
        $target = Social_post::findOrFail($this->targetPostId);
        $comment = $target->comments()->find($commentId);

        if ($comment && (int) auth()->id() === (int) $comment->user_id) {
            if ($comment->media_path) {
                Storage::disk('public')->delete($comment->media_path);
            }
            $comment->delete();
            $this->commentsCount--;
            $target->refresh();
        }
    }

    #[On('follow-updated')]
    public function updateFollowState()
    {
        $target = Social_post::find($this->targetPostId);
        if ($target) {
            $this->isFollowing = auth()->user()->following()
                ->where('followed_id', $target->user_id)
                ->exists();
        }
    }

    public function repost()
    {
        $target = Social_post::findOrFail($this->targetPostId);

        $existe = Social_post::where('user_id', auth()->id())
            ->where('original_post_id', $target->id)
            ->first();

        if ($existe) {
            $existe->delete();
            $this->hasReposted = false;
        } else {
            Social_post::create([
                'user_id' => auth()->id(),
                'original_post_id' => $target->id,
            ]);
            $this->hasReposted = true;

            if (auth()->id() !== $target->user_id) {
                F1Notification::create([
                    'user_id' => $target->user_id,
                    'actor_id' => auth()->id(),
                    'type' => 'repost',
                    'notifiable_id' => $target->id,
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
