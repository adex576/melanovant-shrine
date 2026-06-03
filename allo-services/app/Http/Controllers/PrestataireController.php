<?php
namespace App\Http\Controllers;

use App\Models\PrestataireProfile;
use Illuminate\Http\Request;

class PrestataireController extends Controller
{
    public function index(Request $request)
    {
        $query = PrestataireProfile::with(['user', 'category']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('availability')) {
            $query->where('availability', $request->availability);
        }

        return response()->json($query->paginate(15));
    }

    public function updateProfile(Request $request)
    {
        if (!$this->currentUser()->isPrestataire()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'bio'          => 'nullable|string|max:1000',
            'availability' => 'boolean',
        ]);

        $profile = PrestataireProfile::updateOrCreate(
            ['user_id' => $this->currentUser()->id],
            [
                'category_id'  => $request->category_id,
                'bio'          => $request->bio,
                'availability' => $request->availability ?? true,
            ]
        );

        return response()->json($profile);
    }

    public function show(int $id)
    {
        $profile = PrestataireProfile::with(['user', 'category'])
            ->where('user_id', $id)
            ->firstOrFail();

        return response()->json($profile);
    }
}
