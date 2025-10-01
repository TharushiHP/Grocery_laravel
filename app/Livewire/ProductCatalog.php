<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $sortBy = 'name';
    public $sortOrder = 'asc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortOrder' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortOrder = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->sortBy = 'name';
        $this->sortOrder = 'asc';
        $this->resetPage();
    }

    public function getProducts()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if ($this->selectedCategory) {
            $query->where('category', $this->selectedCategory);
        }

        return $query->orderBy($this->sortBy, $this->sortOrder)
                     ->paginate(12);
    }

    public function getCategories()
    {
        return Product::distinct()->pluck('category')->filter()->sort();
    }

    public function addToCart($productId, $quantity = 1)
    {
        $this->dispatch('add-to-cart', $productId, $quantity);
        session()->flash('success', 'Item added to cart!');
    }

    public function render()
    {
        return view('livewire.product-catalog', [
            'products' => $this->getProducts(),
            'categories' => $this->getCategories()
        ]);
    }
}