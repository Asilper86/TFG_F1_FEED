<?php

namespace App\Livewire;

use App\Models\Social_post;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{

    use WithFileUploads;
    public $content;
    public $media;

    public function save(){
        $this->validate([
            'content'=> 'required_without:media|string|max:500',
            'media'=> 'nullable|image|max:10240',
        ]);

        $path = null;
        if($this->media){
            $path = $this->media->store('social_post', 'public');
        }

        Social_post::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'media_path' => $path,

        ]);

        $this->reset(['content', 'media']);
        $this->dispatch('post-created');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
