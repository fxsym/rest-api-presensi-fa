<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);
        $users = User::with('honor', 'presences')
            ->orderBy('name', 'asc') // urutkan berdasarkan abjad (A-Z)
            ->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => UserResource::collection($users),
        ], 200);
    }

    public function getUser()
    {
        $user = Auth::user();
        return response()->json([
            'message' => 'Get User Succesfully',
            'user' => new UserResource(User::with(['honor'])->findOrFail($user->id))
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'nim' => 'required|string|max:10|unique:users,nim',
    //         'class' => 'required|string',
    //         'phone' => 'required|string|max:15|unique:users,phone',
    //         'username' => 'required|string|max:255|unique:users,username',
    //         'email' => 'required|email|max:255|unique:users,email',
    //         'password' => 'required|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     // Simpan gambar jika ada
    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('images', 'public');
    //     }

    //     $user = User::create([
    //         'name' => $validated['name'],
    //         'nim' => $validated['nim'],
    //         'class' => $validated['class'],
    //         'phone' => $validated['phone'],
    //         'username' => $validated['username'],
    //         'email' => $validated['email'],
    //         'password' => Hash::make($validated['password']),
    //         'image' => $imagePath ?? null,
    //         'presence' => 0,
    //         'role' => 'member',
    //         'status' => 'inactive',
    //         'honors_id' => 11,
    //     ]);

    //     return response()->json([
    //         'message' => 'User created successfully',
    //         'user' => $user
    //     ], 201);
    // }

    // Upload image ke cloudinary
    public function store(Request $request)
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:10|unique:users,nim',
            'class' => 'required|string',
            'phone' => 'required|string|max:15|unique:users,phone',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $uploadedUrl = null;
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $publicId = date('Ymd_His') . '_' . $fileName;

            $result = $cloudinary->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                [
                    'public_id' => $publicId,
                    'folder' => 'user-images', // folder di Cloudinary untuk user images
                ]
            );

            $uploadedUrl = $result['secure_url']; // URL Cloudinary
        }

        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'class' => $validated['class'],
            'phone' => $validated['phone'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'image' => $uploadedUrl,  // simpan URL Cloudinary atau null
            'presence' => 0,
            'role' => 'member',
            'status' => 'inactive',
            'honors_id' => 11,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('view', $user);

        // Ubah path gambar ke URL
        $user->image = $user->image
            ? asset('storage/' . $user->image)
            : null;

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Update gambar ke lokal
    // public function update(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);
    //     Gate::authorize('update', $user);

    //     // Validasi input
    //     $validated = $request->validate([
    //         'name' => 'sometimes|required|string',
    //         'nim' => 'sometimes|required|string|max:10|unique:users,nim,' . $user->id,
    //         'class' => 'sometimes|required|string',
    //         'phone' => 'sometimes|required|string|max:15|unique:users,phone,' . $user->id,
    //         'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
    //         'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
    //         'password' => 'sometimes|required|string|min:8',
    //         'image' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048',
    //         'presence' => 'sometimes|required|integer',
    //         'role' => 'sometimes|required|string',
    //         'status' => 'sometimes|required|string',
    //         'honors_id' => 'sometimes|required|exists:honors,id',
    //     ]);

    //     // Cek jika ada gambar baru yang dikirim
    //     if ($request->hasFile('image')) {
    //         // Simpan gambar baru
    //         $imagePath = $request->file('image')->store('images', 'public');

    //         // Hapus gambar lama jika ada
    //         if ($user->image) {
    //             Storage::disk('public')->delete($user->image);
    //         }

    //         $user->image = $imagePath;
    //     }

    //     // Perbarui hanya field yang ada dalam request
    //     $user->update([
    //         'name' => $validated['name'] ?? $user->name,
    //         'nim' => $validated['nim'] ?? $user->nim,
    //         'class' => $validated['class'] ?? $user->class,
    //         'phone' => $validated['phone'] ?? $user->phone,
    //         'username' => $validated['username'] ?? $user->username,
    //         'email' => $validated['email'] ?? $user->email,
    //         'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
    //         'presence' => $validated['presence'] ?? $user->presence,
    //         'role' => $validated['role'] ?? $user->role,
    //         'status' => $validated['status'] ?? $user->status,
    //         'honors_id' => $validated['honors_id'] ?? $user->honors_id,
    //     ]);

    //     return response()->json([
    //         'message' => 'User updated successfully',
    //         'user' => new UserResource(User::with(['honor'])->findOrFail($user->id))
    //     ], 200);
    // }

    //Update gambar ke Cloudinary
    public function update(Request $request, string $id)
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $user = User::findOrFail($id);
        Gate::authorize('update', $user);

        // Validasi input
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'nim' => 'sometimes|required|string|max:10|unique:users,nim,' . $user->id,
            'class' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string|max:15|unique:users,phone,' . $user->id,
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048',
            'presence' => 'sometimes|required|integer',
            'role' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'honors_id' => 'sometimes|required|exists:honors,id',
        ]);

        // Upload gambar baru ke Cloudinary jika ada
        if ($request->hasFile('image')) {
            // Upload ke Cloudinary
            $originalName = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $publicId = 'user-images/' . $user->id . '_' . date('Ymd_His') . '_' . $fileName;

            $result = $cloudinary->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                [
                    'public_id' => $publicId,
                    'folder' => 'user-images',
                    'overwrite' => true,
                    'invalidate' => true,
                ]
            );

            $uploadedUrl = $result['secure_url'];

            // (Opsional) Hapus gambar lama dari Cloudinary
            // Jika kolom image menyimpan URL lama, kamu bisa parse public_id dan hapus:
            if ($user->image) {
                // Contoh ambil public_id dari URL lama
                // Perlu sesuaikan kalau URL tidak standar
                $oldPublicId = null;
                if (preg_match('#/user-images/(.+)\.(jpg|jpeg|png)$#', $user->image, $matches)) {
                    $oldPublicId = 'user-images/' . $matches[1];
                }
                if ($oldPublicId) {
                    $cloudinary->uploadApi()->destroy($oldPublicId, ['invalidate' => true]);
                }
            }

            $user->image = $uploadedUrl;
        }

        // Update data lain
        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'nim' => $validated['nim'] ?? $user->nim,
            'class' => $validated['class'] ?? $user->class,
            'phone' => $validated['phone'] ?? $user->phone,
            'username' => $validated['username'] ?? $user->username,
            'email' => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
            'presence' => $validated['presence'] ?? $user->presence,
            'role' => $validated['role'] ?? $user->role,
            'status' => $validated['status'] ?? $user->status,
            'honors_id' => $validated['honors_id'] ?? $user->honors_id,
            'image' => $user->image, // sudah diset di atas jika ada file baru
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource(User::with(['honor'])->findOrFail($user->id))
        ], 200);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);
        $user->presences()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Data deleted successfully'
        ], 200);
    }
}
