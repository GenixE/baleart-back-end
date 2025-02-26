<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with(['comments', 'spaces'])->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['comments', 'spaces'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['data' => $user]);
    }

    /**
     * Display the specified user by email with their spaces, comments and images.
     */
    public function showByEmail($email)
    {
        $user = User::where('email', $email)
            ->with(['spaces', 'comments.images'])  // Eager load relationships
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Structure the response data
        $responseData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'phone' => $user->phone,
                'role_id' => $user->role_id,
            ],
            'spaces' => $user->spaces->map(function ($space) {
                return [
                    'id' => $space->id,
                    'name' => $space->name,
                    'description' => $space->observation_CA,
                    // Add other space fields you want to include
                ];
            }),
            'comments' => $user->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'images' => $comment->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            // Add other image fields you want to include
                        ];
                    }),
                    // Add other comment fields you want to include
                ];
            })
        ];

        return response()->json(['data' => $responseData]);
    }

    /**
     * Update the specified user by email.
     */
    public function updateByEmail(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:100',
            'lastName' => 'string|max:100',
            'email' => 'string|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'string|max:100',
            'role_id' => 'integer|exists:roles,id',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|min:8|confirmed|different:current_password',
            'password_confirmation' => 'sometimes|required_with:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Flag to track if password was updated
        $passwordUpdated = false;

        // Verify current password if attempting to change password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['The provided password does not match our records.']]
                ], 422);
            }
            $passwordUpdated = true;
        }

        // Get the fields to update
        $dataToUpdate = $request->only([
            'name',
            'lastName',
            'email',
            'phone',
            'role_id'
        ]);

        // If password is provided and current password was verified, update password
        if ($passwordUpdated) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        // Prepare the response message
        $message = $passwordUpdated
            ? 'User profile and password updated successfully'
            : 'User profile updated successfully';

        return response()->json([
            'message' => $message,
            'data' => $user,
            'updates' => [
                'profile_updated' => count(array_diff_assoc($dataToUpdate, ['password' => $user->password])) > 0,
                'password_updated' => $passwordUpdated
            ],
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Remove the specified user by email and all related records.
     */
    public function destroyByEmail($email)
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Check if user is a manager
            if ($user->role_id === 2) {
                return response()->json([
                    'message' => 'Manager accounts cannot be deleted through the API. Please use the backoffice.'
                ], 403);
            }

            // 1. First delete all images associated with user's comments
            foreach ($user->comments as $comment) {
                // Delete all images for this comment
                $comment->images()->delete();
            }

            // 2. Now safe to delete all comments
            $user->comments()->delete();

            // 3. For spaces, first delete service_space relationships
            foreach ($user->spaces as $space) {
                $space->services()->detach(); // This removes entries from service_space table
            }

            // 4. Now safe to delete spaces
            $user->spaces()->delete();

            // 5. Finally delete the user
            $user->delete();

            DB::commit();

            return response()->json(['message' => 'User account and all related content deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting user and related records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:100',
            'lastName' => 'string|max:100',
            'email' => 'string|email|max:100|unique:users,email,' . $id,
            'phone' => 'string|max:100',
            'role_id' => 'integer|exists:roles,id',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|min:8|confirmed|different:current_password',
            'password_confirmation' => 'sometimes|required_with:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Flag to track if password was updated
        $passwordUpdated = false;

        // Verify current password if attempting to change password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['The provided password does not match our records.']]
                ], 422);
            }
            $passwordUpdated = true;
        }

        // Get the fields to update
        $dataToUpdate = $request->only([
            'name',
            'lastName',
            'email',
            'phone',
            'role_id'
        ]);

        // If password is provided and current password was verified, update password
        if ($passwordUpdated) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        // Prepare the response message
        $message = $passwordUpdated
            ? 'User profile and password updated successfully'
            : 'User profile updated successfully';

        return response()->json([
            'message' => $message,
            'data' => $user,
            'updates' => [
                'profile_updated' => count(array_diff_assoc($dataToUpdate, ['password' => $user->password])) > 0,
                'password_updated' => $passwordUpdated
            ],
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Reset user password (simplified for school project)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
            'user' => [
                'id' => $user->id,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Remove the specified user and all related records.
     */
    public function destroy($id)
    {

    }
}
