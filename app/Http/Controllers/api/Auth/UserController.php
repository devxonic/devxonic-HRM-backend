<?php

namespace App\Http\Controllers\api\auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function store(Request $request)
    {
        try {

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create a new user
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;

            // Save the User
            $user->save();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);


        } catch (\Illuminate\Database\QueryException $e) {
            // Handle Database Error
            return response()->json([
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);


        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'error' => 'An unexpected error occured: ' . $e->getMessage()
            ], 500);
        }
    }



    public function show($id)
    {
        try {

            // Find the user by ID
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
            return response()->json([
                'user' => $user
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case when the user is not found
            return response()->json([
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            // Handle other unexpected errors
            return response()->json([
                'error' => 'An unexpected error occurred ' . $e->getMessage()
            ], 500);
        }
    }




    public function destroy($id)
    {
        try {

            // Find the user by ID
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            // Delete the User
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case when the user is not found
            return response()->json([
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            // Handle other unexpected errors
            return response()->json([
                'error' => 'An unexpected error occurred ' . $e->getMessage()
            ], 500);
        }
    }



    public function fetch()
    {
        try {
            $users = User::all();
            return response()->json($users);

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'error' => 'An unexpected error occurred ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            // FIND THE USER BY ID
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            // UPDATE USER FIELDS
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->role = $request->role;

            //SAVE THE USER 
            $user->save();

            return response()->json([
                'message' => 'User Updated Successfully',
                'user' => $user
            ], 200);

        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            // Handle the case when the user is not found
            return response()->json([
                 'message' => 'User not found'
            ], 404);

        }catch(\Exception $e){
            // Handle other unexpected errors
            return response()->json([
                'error' => 'An unexpected error occurred ' .$e->getMessage()
            ], 500);
        }
    }

}
