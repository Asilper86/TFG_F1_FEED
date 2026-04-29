<?php

namespace App\Livewire;

use App\Models\Social_post;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class UserProfile extends Component
{
    public User $profileUser;
    public $followersCount = 0;
    public $followingCount = 0;
    public $isFollowing = false;
    public $showEditModal = false;
    public $username;
    public $bio;
    public $showFollowersModal = false;
    public $showFollowingModal = false;

    public function openEditModal(){
        $this->username = $this->profileUser->username;
        $this->bio = $this->profileUser->bio;
        $this->showEditModal = true;
    }

    public function openFollowersModal(){
        $this->showFollowersModal = true;
    }

    public function openFollowingModal(){
        $this->showFollowingModal = true;
    }

    public function saveProfile(){
        $this->validate([
            'username' => 'nullable|string|max:20|unique:users,username,'.auth()->id(),
            'bio' => 'nullable|string|max:160'
        ]);

        auth()->user()->update([
            'username' => $this->username,
            'bio' => $this->bio
        ]);

        $this->profileUser->refresh();
        $this->showEditModal = false;
    }

    public function mount($user = null){
        if($user){
            $this->profileUser = User::findOrFail($user);
        } else {
            $this->profileUser = auth()->user();
        }

        $this->followersCount = $this->profileUser->followers()->count();
        $this->followingCount = $this->profileUser->following()->count();

        if(auth()->id() !== $this->profileUser->id){
            $this->isFollowing = auth()->user()->following()->where('followed_id', $this->profileUser->id)->exists();
        }
    }

    public function toggleFollow(){
        if(auth()->id() !== $this->profileUser->id){
            auth()->user()->following()->toggle($this->profileUser->id);
            $this->isFollowing = !$this->isFollowing;
            $this->followersCount = $this->profileUser->followers()->count();
            $this->dispatch('follow-updated');  
        }
    }

    #[On('post-deleted')]
    public function refreshProfile()
    {
        // El render se encargará de refrescar los posts
    }

    public function render()
    {
        $posts = Social_post::with(['user', 'likes', 'lap.session', 'comments.user'])
            ->where('user_id', $this->profileUser->id)
            ->latest()
            ->get();

        return view('livewire.user-profile', compact('posts'))->layout('layouts.app');
    }
}
