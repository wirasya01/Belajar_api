<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Str;
use Validator;
use Storage;

class BeritaController extends Controller
{

    public function index()
    {
        $berita = Berita::with('kategori', 'tag', 'user')->latest()->get();
        return response()->json([
            'succes' => true,
            'message' => 'daftar berita',
            'data' => $berita,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'tag' => 'required|array',
            'id_user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // upload foto
            $path = $request->File('foto')->store('public/berita');

            $berita = new Berita;
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->judul);
            $berita->deskripsi = $request->deskripsi;
            $berita->foto = $path;
            $berita->id_kategori = $request->id_kategori;
            $berita->id_user = $request->id_user;
            $berita->save();

            // Lampiran Banyak Tag
            $berita->tag()->attach($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Data Berita Berhasil Ditambahkan',
                'data' => $berita,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $berita = Berita::findOrFail($id)->with('kategori', 'tag', 'user')->first();
            return response()->json([
                'succes' => true,
                'message' => 'berita berhasil ditemukan',
                'errors' => $e->geyMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'succes' => false,
                'message' => 'berita tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'tag' => 'required|array',
            'id_user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // upload foto
            // $path = $request->File('foto')->store('public/berita');

            $berita = Berita::findOrFail($id);
            //hapus foto lama
            if ($request->hasFile('foto')) {
                Storage::delete($berita->foto);
                $path = $request->file('foto')->store('berita');
                $berita->foto = $path;
            }
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->judul);
            $berita->deskripsi = $request->deskripsi;
            // $berita->foto = $path;
            $berita->id_kategori = $request->id_kategori;
            $berita->id_user = $request->id_user;
            $berita->save();

            // Lampiran Banyak Tag
            $berita->tag()->sync($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Data Berita Berhasil Ditambahkan',
                'data' => $berita,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $berita = Berita::findOrFail($id);
            //hapus tag berita
            $berita->tag()->detach();
            //hapus foto
            Storage::delete($berita->foto);
            $berita->delete();
            return response()->json([
                'succes' => true,
                'message' => 'berita' . $berita->judul . 'berhasil di hapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'succes' => false,
                'message' => 'berita tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
