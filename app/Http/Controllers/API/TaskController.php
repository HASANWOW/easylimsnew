<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * 1. READ ALL (Menampilkan semua data tugas)
     * URL: GET /api/tasks
     */
    public function index()
    {
        // Mengambil semua data Task, sekaligus membawa data nama Project-nya (relasi 'project')
        $tasks = Task::with('project')->get();
        return response()->json($tasks, 200);
    }

    /**
     * 2. CREATE (Menyimpan data tugas baru)
     * URL: POST /api/tasks
     */
    public function store(Request $request)
    {
        // Satpam Validasi: Mengecek apakah isian form sudah sesuai aturan
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id', // Harus diisi & ID proyek harus ada di database
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        // Jika validasi gagal, kembalikan pesan error 400 (Bad Request)
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        // Jika berhasil, suruh Model untuk menyimpan datanya ke tabel 'tasks'
        $task = Task::create($request->all());
        
        // Kembalikan kotak JSON berisi data yang baru dibuat dengan stempel 201 (Created)
        return response()->json($task, 201);
    }

    /**
     * 3. READ DETAIL (Menampilkan satu data tugas secara spesifik)
     * URL: GET /api/tasks/{id}
     */
    public function show(string $id)
    {
        // Cari tugas berdasarkan ID yang diminta
        $task = Task::with('project')->find($id);
        
        // Jika tugas tidak ditemukan di database
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        
        // Jika ditemukan, tampilkan datanya
        return response()->json($task, 200);
    }

    /**
     * 4. UPDATE (Mengubah/mengedit data tugas yang sudah ada)
     * URL: PUT /api/tasks/{id}
     */
    public function update(Request $request, string $id)
    {
        // 1. Cari dulu datanya ada atau tidak
        $task = Task::with('project')->find($id);
        
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        } // <-- Di kodemu sebelumnya, kurung tutup ini hilang!

        // 2. Jika data ada, cek lagi isian form perubahannya (Validasi)
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        // 3. Timpa data lama dengan data baru yang dikirimkan (Update)
        $task->project_id = $request->project_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save(); // Simpan perubahan ke database

        // 4. Berikan konfirmasi sukses dengan stempel 200 (OK)
        return response()->json([
            'message' => 'Task Updated',
            'data' => $task
        ], 201);
    }

    /**
     * 5. DELETE (Menghapus data tugas)
     * URL: DELETE /api/tasks/{id}
     */
    public function destroy(string $id)
    {
        // 1. Cari tugas yang mau dihapus berdasarkan ID
        $task = Task::find($id); // Tidak perlu 'with(project)' kalau cuma mau dihapus
        
        // 2. Jika datanya tidak ada
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        } // <-- Di kodemu sebelumnya, kurung tutup ini juga hilang!

        // (Validator dihapus dari sini karena kita tidak mengecek form saat menghapus)

        // 3. Eksekusi penghapusan data
        $task->delete();
        
        // 4. Berikan pesan konfirmasi bahwa data sukses dihapus
        return response()->json(['message' => 'Task Deleted'], 200);
    }
}