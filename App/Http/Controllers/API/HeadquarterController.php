<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Headquarter;
use App\Models\AppUser;
use Illuminate\Http\Request;

class HeadquarterController extends Controller
{
    /**
     * Display a listing of headquarters.
     */
    public function index()
    {
        return response()->json(Headquarter::with('manager')->get(), 200);
    }

    /**
     * Store a newly created headquarter in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:app_users,id',
        ]);

        $manager = AppUser::find($request->manager_id);
        if (!$manager || !$manager->isManager()) {
            return response()->json(['error' => 'Unauthorized The user is not a manager'], 400);
        }

        $headquarter = Headquarter::create($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $headquarter
        ], 201);
    }

    /**
     * Display the specified headquarter.
     */
    public function show($id)
    {
        $headquarter = Headquarter::with('manager')->find($id);

        if (!$headquarter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Headquarter not found'
            ], 404);
        }

        return response()->json($headquarter, 200);
    }

    /**
     * Update the specified headquarter in storage.
     */
    public function update(Request $request, $id)
    {
        $headquarter = Headquarter::find($id);

        if (!$headquarter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Headquarter not found'
            ], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'manager_id' => 'exists:app_users,id',
        ]);

        if ($request->filled('manager_id')) {
            $manager = AppUser::find($request->manager_id);
            if (!$manager || !$manager->isManager()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The selected manager is not valid. The user must have the Manager role.'
                ], 400);
            }
        }

        $headquarter->update($request->only(['name', 'manager_id']));

        return response()->json([
            'status' => 'success',
            'data' => $headquarter
        ], 200);
    }

    /**
     * Remove the specified headquarter from storage.
     */
    public function destroy($id)
    {
        $headquarter = Headquarter::find($id);

        if (!$headquarter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Headquarter not found'
            ], 404);
        }

        $headquarter->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Headquarter deleted successfully'
        ], 200);
    }

    /**
     * Assign an adviser to a headquarter.
     */
    public function assignAdviserToHeadquarter(Request $request, $headquarterId)
    {
        try {
            $request->validate([
                'adviser_id' => 'required|exists:app_users,id',
            ]);

            $adviser = AppUser::find($request->adviser_id);
            if (!$adviser || !$adviser->isAdviser()) {
                return response()->json(['error' => 'The user does not have the role of adviser.'], 403);
            }

            $headquarter = Headquarter::findOrFail($headquarterId);
            $headquarter->advisers()->attach($request->adviser_id);

            return response()->json(['message' => 'Adviser assigned to the headquarter successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error assigning adviser to headquarter', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove an adviser from a headquarter.
     */
    public function removeAdviserFromHeadquarter(Request $request, $headquarterId)
    {
        try {
            $request->validate([
                'adviser_id' => 'required|exists:app_users,id',
            ]);

            $adviser = AppUser::find($request->adviser_id);
            if (!$adviser || !$adviser->isAdviser()) {
                return response()->json(['error' => 'The user does not have the role of adviser.'], 403);
            }

            $headquarter = Headquarter::findOrFail($headquarterId);

            if (!$headquarter->advisers()->where('user_id', $request->adviser_id)->exists()) {
                return response()->json(['error' => 'The adviser is not assigned to this headquarter.'], 404);
            }

            $headquarter->advisers()->detach($request->adviser_id);

            return response()->json(['message' => 'Adviser removed from the headquarter successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error removing adviser from headquarter', 'details' => $e->getMessage()], 500);
        }
    }
}
