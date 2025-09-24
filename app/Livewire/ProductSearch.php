<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductSearch extends Component
{
    public $search = '';
    public $results = [];
    public $showResults = false;

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->results = Product::search($this->search)
                ->limit(5)
                ->get();
            $this->showResults = true;
        } else {
            $this->results = [];
            $this->showResults = false;
        }
    }

    public function selectProduct($productId)
    {
        $this->search = '';
        $this->showResults = false;
        $this->dispatch('product-selected', productId: $productId);
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
