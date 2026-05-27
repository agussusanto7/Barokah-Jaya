<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
#[Title('Produk - Toko Barokah Jaya')]
class ProductIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categoryFilter = '';
    public $productId;
    public $name = '';
    public $sku = '';
    public $category_id = '';
    public $description = '';
    public $price = '';
    public $stock = '';
    public $image;
    public $oldImage;
    public $isEdit = false;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|min:3',
        'sku' => 'required|unique:products,sku',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'description' => 'nullable',
        'image' => 'nullable|image|max:2048'
    ];

    protected $messages = [
        'name.required' => 'Nama produk wajib diisi',
        'sku.required' => 'SKU wajib diisi',
        'sku.unique' => 'SKU sudah digunakan',
        'category_id.required' => 'Kategori wajib dipilih',
        'price.required' => 'Harga wajib diisi',
        'price.numeric' => 'Harga harus berupa angka',
        'stock.required' => 'Stok wajib diisi',
        'stock.integer' => 'Stok harus berupa angka',
        'image.image' => 'File harus berupa gambar',
        'image.max' => 'Ukuran gambar maksimal 2MB',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'sku', 'category_id', 'description', 'price', 'stock', 'image', 'oldImage', 'productId', 'isEdit']);
        $this->resetValidation();
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['sku'] = 'required|unique:products,sku,' . $this->productId;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ];

        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
            $data['image'] = $imagePath;

            if ($this->isEdit && $this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }
        }

        if ($this->isEdit) {
            $product = Product::find($this->productId);
            $product->update($data);
            session()->flash('message', 'Produk berhasil diupdate');
        } else {
            Product::create($data);
            session()->flash('message', 'Produk berhasil ditambahkan');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = $product->category_id;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->oldImage = $product->image;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        session()->flash('message', 'Produk berhasil dihapus');
    }

    public function render()
    {
        $query = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%');

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('livewire.products.product-index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
