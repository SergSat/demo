<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Models\Position;
use App\Models\RegistrationToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Services\ImageService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $token = session('registration_token');
        $tokenExpiresAt = session('registration_token_expires_at');

        if (!$token || !$tokenExpiresAt || now()->greaterThan($tokenExpiresAt)) {
            // Generate a new token if it doesn't exist or has expired
            $token = RegistrationToken::generateToken();
            session([
                'registration_token' => $token,
                'registration_token_expires_at' => now()->addMinutes(40)
            ]);
        }

        return view('auth.register', ['positions' => Position::all(), 'token' => $token]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $tokenFromRequest = $request->input('registration_token');
        $registrationToken = RegistrationToken::where('token', $tokenFromRequest)->first();

        // Check if the token is valid and not expired
        if (!$registrationToken || now()->gt($registrationToken->expires_at)) {
            return redirect()->back()->withErrors(['token' => 'The registration token is invalid or expired.']);
        }

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

        event(new Registered($user));

        Auth::login($user);

        // Delete the token after successful registration
        $registrationToken->delete();
        session()->forget(['registration_token', 'registration_token_expires_at']);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Generate and store a resized photo for the user during registration.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @return string
     */
    private function generateResizedPhoto($uploadedFile): string
    {
        $imageManager = new ImageManager(new Driver());

        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        copy($uploadedFile->getRealPath(), $tempFilePath);

        $fileName = Str::random(10) . '.jpg';
        $filePath = storage_path('app/public/users/' . $fileName);

        $image = $imageManager->read($tempFilePath);

        $image->resize(70, 70);

        $image->save($filePath);

        fclose($tempFile);

        return 'storage/users/' . $fileName;
    }
}
