<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class ProductImagePreview extends Component
{
    use WithFileUploads;

    public $image;
    public $existingImage; // for edit mode

    protected $rules = [
        'image' => 'nullable|image|max:2048',
    ];

    public function mount($existingImage = null)
    {
        $this->existingImage = $existingImage;
    }

    public function render()
    {
        return view('livewire.admin.product-image-preview');
    }
}
