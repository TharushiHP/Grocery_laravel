<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;

class AdminProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $productId;
    
    // Product form fields
    public $name = '';
    public $description = '';
    public $price = '';
    public $category = '';
    public $quantity = '';
    public $image;
    public $existingImage = '';

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'description' => 'nullable|max:1000',
        'price' => 'required|numeric|min:0',
        'category' => 'required|max:100',
        'quantity' => 'required|max:20',
        'image' => 'nullable|image|max:2048',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->category = $product->category;
        $this->quantity = $product->quantity;
        $this->existingImage = $product->image;
        
        $this->showModal = true;
        $this->editMode = true;
    }

    public function saveProduct()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'quantity' => $this->quantity,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        }

        if ($this->editMode) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);
            session()->flash('message', 'Product updated successfully!');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created successfully!');
        }

        $this->closeModal();
    }

    public function deleteProduct($id)
    {
        Product::findOrFail($id)->delete();
        session()->flash('message', 'Product deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->category = '';
        $this->quantity = '';
        $this->image = null;
        $this->existingImage = '';
        $this->productId = null;
        $this->editMode = false;
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($query) => $query->search($this->search))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-product-manager', compact('products'));
    }
}
