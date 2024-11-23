<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\IndexUserRequest;
use App\Http\Requests\API\StoreUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Position;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request)
    {
        $count = $request->input('count', 5);

        $users = User::orderBy('id', 'asc')->paginate($count);

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $position = Position::findOrFail($validated['position_id']);

        $imageManager = new ImageManager(new Driver());
        $imageService = new ImageService($imageManager);
        $photoPath = $imageService->uploadResizedPhotoToStorage(
            $request->file('photo'),
            100,
            100,
            'users'
        );

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'position' => $position->name,
            'position_id' => $position->id,
            'photo' => $photoPath,
            'phone' => $validated['phone'],
        ]);

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'message' => 'New user successfully registered',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $request->merge(['id' => $id]);

        // Validate the request
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $user = User::find($validated['id']);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
