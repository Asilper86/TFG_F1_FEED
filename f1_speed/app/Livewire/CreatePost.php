<?php

namespace App\Livewire;

use App\Models\Hashtag;
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
            'media'=> 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $path = null;
        if($this->media){
            $path = $this->media->store('social_post', 'public');
        }

        $post = Social_post::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'media_path' => $path,

        ]);

        preg_match_all('/#(\w+)/', $this->content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $tagName) {
                $hashtag = Hashtag::firstOrCreate(['name' => strtolower($tagName)]);
                $post->hashtags()->attach($hashtag->id);
            }
        }

        $this->reset(['content', 'media']);
        $this->dispatch('post-created');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
