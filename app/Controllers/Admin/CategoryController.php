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
        ];

        return view('admin/categories/index', $data);
    }

    /**
     * DataTable server-side endpoint
     */
    public function datatable()
    {
        $request = $this->request;
        
        // Get DataTable parameters
        $draw = intval($request->getGet('draw') ?? 0);
        $start = intval($request->getGet('start') ?? 0);
        $length = intval($request->getGet('length') ?? 10);
        
        // Get search value
        $searchValue = $request->getGet('search');
        $search = is_array($searchValue) ? ($searchValue['value'] ?? '') : '';
        
        // Get order parameters
        $orderData = $request->getGet('order');
        $orderCol = 0;
        $orderDir = 'asc';
        
        if (is_array($orderData) && isset($orderData[0])) {
            $orderCol = intval($orderData[0]['column'] ?? 0);
            $orderDir = $orderData[0]['dir'] ?? 'asc';
        }

        // Column mapping
        $columns = [
            0 => 'categories.id',
            1 => 'categories.name',
            2 => 'categories.description',
            3 => 'product_count',
            4 => 'categories.id' // Actions
        ];
        
        $orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'categories.name';
        $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

        // Build query with product count
        $db = \Config\Database::connect();
        $builder = $db->table('categories');
        $builder->select('categories.*, COUNT(products.id) as product_count')
                ->join('products', 'products.category_id = categories.id', 'left')
                ->groupBy('categories.id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('categories.name', $search)
                ->orLike('categories.description', $search)
            ->groupEnd();
        }

        // Get total records
        $totalRecords = $this->categoryModel->countAll();
        
        // Get filtered records count
        $builderCount = clone $builder;
        $filteredRecords = $builderCount->countAllResults(false);

        // Apply ordering and pagination
        $categories = $builder->orderBy($orderBy, $orderDir)
                             ->limit($length, $start)
                             ->get()
                             ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($categories as $index => $category) {
            $data[] = [
                $start + $index + 1,
                '<strong>' . esc($category['name']) . '</strong>',
                esc($category['description'] ?? '-'),
                '<span class="badge bg-primary">' . $category['product_count'] . ' produk</span>',
                view('admin/categories/_actions', ['category' => $category]),
            ];
        }

        // Return JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
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
