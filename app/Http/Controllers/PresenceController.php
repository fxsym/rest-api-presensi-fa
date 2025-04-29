<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PresenceResource;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Gate::authorize('viewAny', Presence::class);
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $presences = Presence::with('user')->get(); // Admin
        } elseif ($user->role === 'member') {
            $presences = Presence::with('user')->where('user_id', $user->id)->get(); // Member
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
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'lab' => 'required|string',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        // Simpan gambar
        $imagePath = $request->file('image')->store('presences', 'public');

        // Membuat data presensi baru
        $presence = Presence::create([
            'image' => $imagePath,
            'lab' => $validated['lab'],
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
            'user_id' => $validated['user_id'],
        ]);

        return response()->json([
            'message' => 'Presence created successfully',
            'presence' => new PresenceResource(Presence::with(['user'])->findOrFail($presence->id)),
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
    public function edit(Presence $presence)
    {
        //
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
