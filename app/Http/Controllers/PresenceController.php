<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PresenceResource;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Cloudinary;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Gate::authorize('viewAny', Presence::class);
        $user = Auth::user();

        if ($user->role === 'admin') {
            $presences = Presence::with('user')
                ->orderBy('updated_at', 'desc') // urutkan dari yang terbaru
                ->get();
        } elseif ($user->role === 'member') {
            $presences = Presence::with('user')
                ->where('user_id', $user->id)
                ->orderBy('updated_at', 'desc') // urutkan dari yang terbaru
                ->get();
        } else {
            return response()->json([
                'message' => 'Upss... i dont know what you role it is..'
            ], 403);
        }

        return response()->json([
            'message' => 'Presences retrieved successfully',
            'presences' => PresenceResource::collection($presences),
        ], 200);
    }

    public function updatePresenceStatus(Request $request, $id)
    {
        $presence = Presence::with('user')->findOrFail($id);
        $user = Auth::user();
        Gate::authorize('updateStatus', $presence);
        $validated = $request->validate([
            'status' => 'nullable|string|max:20',
        ]);

        $presence->update([
            'status' => $validated['status'] ?? $presence->status,
        ]);

        return response()->json([
            'message' => 'Presence Status updated successfully',
            'presence' => new PresenceResource($presence),
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
    // Upload image menggunakan lokal
    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     // Validasi input
    //     $validated = $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //         'lab' => 'required|string',
    //         'note' => 'nullable|string',
    //     ]);

    //     // // Simpan gambar
    //     // $imagePath = $request->file('image')->store('presences', 'public');

    //     // Membuat data presensi baru
    //     $presence = Presence::create([
    //         'image' => imagePath, // ambil URL saja
    //         'lab' => $validated['lab'],
    //         'status' => 'pending',
    //         'note' => $validated['note'] ?? null,
    //         'user_id' => $user->id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Presence created successfully',
    //         'presence' => new PresenceResource(Presence::with(['user'])->findOrFail($presence->id)),
    //         'image' => imagePath, // ambil URL saja
    //     ], 201);
    // }

    //Upload image ke cloudinary
    public function store(Request $request)
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
        $user = Auth::user();

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'lab' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $originalName = $request->file('image')->getClientOriginalName();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $publicId = date('Ymd_His') . '_' . $fileName;

        $result = $cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'public_id' => $publicId,
                'folder' => 'presences-images'
            ]
        );

        // $result['secure_url'] sudah URL string
        $uploadedUrl = $result['secure_url'];

        $presence = Presence::create([
            'image' => $uploadedUrl,
            'lab' => $validated['lab'],
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Presence created successfully',
            'presence' => new PresenceResource(Presence::with('user')->findOrFail($presence->id)),
            'image' => $uploadedUrl,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        Gate::authorize('view', $presence);
        $presence->load('user');

        return response()->json([
            'message' => 'Presence retrieved successfully',
            'presence' => new PresenceResource($presence),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        // $presences = Presence::all();

        // foreach ($presences as $presence) {
        //     $randomId = rand(1, 500); // menghasilkan angka acak antara 1-900
        //     $imageUrl = "https://picsum.photos/id/{$randomId}/700/400";

        //     $presence->update([
        //         'image' => $imageUrl,
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ]);
        // }

        // return response()->json([
        //     'message' => 'All presences updated successfully',
        //     'presences' => $presences
        // ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presence $presence)
    {
        Gate::authorize('update', $presence);
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'lab' => 'nullable|string',
            'status' => 'nullable|string|max:20',
            'note' => 'nullable|string'
        ]);

        // Simpan gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($presence->image) {
                Storage::disk('public')->delete($presence->image);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('presences', 'public');
            $presence->image = $imagePath;
        }

        // Update data presensi
        $presence->update([
            'lab' => $validated['lab'] ?? $presence->lab,
            'status' => $validated['status'] ?? $presence->status,
            'note' => $validated['note'] ?? $presence->note
        ]);

        return response()->json([
            'message' => 'Presence updated successfully',
            'presence' => new PresenceResource($presence),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        Gate::authorize('delete', $presence);
        // Hapus gambar dari penyimpanan jika ada
        if ($presence->image) {
            Storage::disk('public')->delete($presence->image);
        }

        // Hapus data presensi dari database
        $presence->delete();

        return response()->json([
            'message' => 'Presence deleted successfully',
        ], 200);
    }
}
