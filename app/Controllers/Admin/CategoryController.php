<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Display category list
     */
    public function index()
    {
        $data = [
            'title'      => 'Kategori Produk',
            'user'       => auth()->user(),
            'categories' => $this->categoryModel->getCategoriesWithProductCount(),
        ];

        return view('admin/categories/index', $data);
    }

    /**
     * Show create category form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori',
            'user'  => auth()->user(),
        ];

        return view('admin/categories/create', $data);
    }

    /**
     * Store new category
     */
    public function store()
    {
        if (!$this->validate($this->categoryModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('/admin/categories')->with('message', 'Kategori berhasil ditambahkan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kategori!');
    }

    /**
     * Show edit category form
     */
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan!');
        }

        $data = [
            'title'    => 'Edit Kategori',
            'user'     => auth()->user(),
            'category' => $category,
        ];

        return view('admin/categories/edit', $data);
    }

    /**
     * Update category
     */
    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan!');
        }

        if (!$this->validate($this->categoryModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('/admin/categories')->with('message', 'Kategori berhasil diupdate!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate kategori!');
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan!');
        }

        // Check if category has products
        if ($this->categoryModel->hasProducts($id)) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk!');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('/admin/categories')->with('message', 'Kategori berhasil dihapus!');
        }

        return redirect()->to('/admin/categories')->with('error', 'Gagal menghapus kategori!');
    }
}
