<?php 
  
namespace App\Controllers; 
  
use CodeIgniter\RESTful\ResourceController; 
use CodeIgniter\API\ResponseTrait; 
use App\Models\ArtikelModel; 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
class Post extends ResourceController 
{ 
    use ResponseTrait; 
    // all users 
    public function index() 
    { 
        $model = new ArtikelModel(); 
        $data['artikel'] = $model->orderBy('id', 'DESC')->findAll(); 
        return $this->respond($data); 
    } 
    // create 
    public function create()
    {
        // Menangkap data input secara fleksibel (bisa form-data, x-www-form-urlencoded, atau JSON)
        $data = [
            'judul'  => $this->request->getVar('judul'),
            'isi'    => $this->request->getVar('isi'),
            'status' => $this->request->getVar('status') ?? 0,
        ];

        // Validasi opsional jika data masih kosong agar tidak crash ke MySQL
        if (empty($data['judul'])) {
            return $this->fail('Input judul tidak boleh kosong!');
        }

        $model = new \App\Models\ArtikelModel(); // Sesuaikan nama class model artikel milikmu

        if ($model->insert($data)) {
            return $this->respondCreated([
                'status'   => 201,
                'message'  => 'Artikel berhasil ditambahkan',
                'data'     => $data
            ]);
        }

        return $this->fail('Gagal menambahkan artikel');
    }
    // single user
    public function show($id = null) 
    { 
        $model = new ArtikelModel(); 
        $data = $model->where('id', $id)->first(); 
        if ($data) { 
            return $this->respond($data); 
        } else { 
            return $this->failNotFound('Data tidak ditemukan.'); 
        } 
    } 
    // update 
    public function update($id = null)
    {
        // Untuk metode PUT/PATCH, gunakan getRawInput() agar data dari URLSearchParams/Axios terbaca sempurna
        $input = $this->request->getRawInput();

        $data = [
            'judul'  => $input['judul'] ?? null,
            'isi'    => $input['isi'] ?? null,
            'status' => $input['status'] ?? 0,
        ];

        if (empty($data['judul'])) {
            return $this->fail('Input judul tidak boleh kosong untuk diubah!');
        }

        $model = new \App\Models\ArtikelModel();

        if ($model->update($id, $data)) {
            return $this->respond([
                'status'   => 200,
                'message'  => 'Artikel berhasil diperbarui',
                'data'     => $data
            ]);
        }

        return $this->fail('Gagal memperbarui artikel');
    } 
    // delete 
    public function delete($id = null) 
    { 
        $model = new ArtikelModel(); 
        $data = $model->where('id', $id)->delete($id); 
        if ($data) { 
            $model->delete($id); 
            $response = [ 
                'status'   => 200, 
                'error'    => null, 
                'messages' => [ 
                    'success' => 'Data artikel berhasil dihapus.' 
                ] 
            ]; 
            return $this->respondDeleted($response); 
        } else { 
            return $this->failNotFound('Data tidak ditemukan.'); 
        } 
    } 
} 