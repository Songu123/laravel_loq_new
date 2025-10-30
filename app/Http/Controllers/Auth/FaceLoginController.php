<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;

class FaceLoginController extends Controller
{
    private $faceApiUrl;
    private $confidenceThreshold;

    public function __construct()
    {
        $this->faceApiUrl = config('services.face_recognition.url', 'http://127.0.0.1:8001');
        $this->confidenceThreshold = config('services.face_recognition.threshold', 0.6);
    }

    /**
     * Show face login form
     */
    public function showForm()
    {
        return view('auth.face-login');
    }

    /**
     * Show face enrollment form (for logged-in admins)
     */
    public function showEnroll()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'Face recognition is only available for admin users.');
        }

        return view('auth.face-enroll', ['user' => $user]);
    }

    /**
     * Enroll face for current admin user
     */
    public function enroll(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face recognition is only available for admin users.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'images' => 'required|array|min:3|max:10',
                'images.*' => 'required|string'
            ]);

            Log::info("Face enrollment request for user: {$user->id}");

            // Call Python API
            $response = Http::timeout(30)->post("{$this->faceApiUrl}/api/face/enroll", [
                'user_id' => $user->id,
                'images' => $request->images
            ]);

            if (!$response->successful()) {
                throw new Exception('Face recognition service error: ' . $response->body());
            }

            $result = $response->json();

            if ($result['success']) {
                Log::info("Face enrollment successful for user: {$user->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Face enrollment successful! You can now login using face recognition.',
                    'embeddings_count' => $result['embeddings_count']
                ]);
            } else {
                throw new Exception($result['message'] ?? 'Face enrollment failed');
            }

        } catch (Exception $e) {
            Log::error("Face enrollment error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify face and login user
     */
    public function verify(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'image' => 'required|string'
            ]);

            Log::info("Face verification request from IP: " . $request->ip());

            // Call Python API for authentication
            $response = Http::timeout(10)->post("{$this->faceApiUrl}/api/face/authenticate", [
                'image' => $request->image
            ]);

            if (!$response->successful()) {
                throw new Exception('Face recognition service error: ' . $response->body());
            }

            $result = $response->json();

            // Check if face matched
            if ($result['success'] && isset($result['user_id'])) {
                // Get user
                $user = User::find($result['user_id']);

                if (!$user) {
                    throw new Exception('User not found');
                }

                if (!$user->isAdmin()) {
                    throw new Exception('Face recognition is only available for admin users');
                }

                if (!$user->is_active) {
                    throw new Exception('User account is inactive');
                }

                // Check confidence threshold
                if ($result['confidence'] < $this->confidenceThreshold) {
                    throw new Exception('Face match confidence too low');
                }

                // Check liveness
                if (!$result['is_live']) {
                    throw new Exception('Liveness check failed. Please use a live camera.');
                }

                // Login user
                Auth::login($user, $remember = true);

                Log::info("Face login successful for user: {$user->id} (confidence: {$result['confidence']})");

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'confidence' => $result['confidence'],
                    'redirect' => route('home')
                ]);
            } else {
                // No match found
                Log::warning("Face verification failed: No matching face found");

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'No matching face found. Please try again or use email/password login.'
                ], 401);
            }

        } catch (Exception $e) {
            Log::error("Face verification error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete enrolled face data
     */
    public function deleteEnrollment(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Clear face data
            $user->face_embeddings = null;
            $user->face_enrolled_at = null;
            $user->face_images = null;
            $user->save();

            Log::info("Face enrollment deleted for user: {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Face enrollment deleted successfully'
            ]);

        } catch (Exception $e) {
            Log::error("Delete enrollment error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enrollment status
     */
    public function enrollmentStatus()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'enrolled' => $user->face_enrolled_at !== null,
            'enrolled_at' => $user->face_enrolled_at,
            'embeddings_count' => $user->face_embeddings ? count(json_decode($user->face_embeddings)) : 0
        ]);
    }
}
