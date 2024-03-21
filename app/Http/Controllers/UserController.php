<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function createuser(Request $request){

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|string|min:8|max:255',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        }


    public function apply_for_loan(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|max:255',
            'amount' => 'required|string|max:255',
            'date_of_birth' => 'required|string|max:255',
            'income' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'status' => 'NEW',
            'amount' => $validatedData['amount'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'income' => $validatedData['income'],
            'address' => $validatedData['address'],
            'password' => $validatedData['password'],
        ]);

        return response()->json(['message' => 'loan applied successfully', 'user' => $user], 201);
    }

    public function update_loan_status(Request $request, $id){
        $validatedData = $request->validate([
            'status' => 'required|string|max:255',
        ]);
        $data = user::find($id);

            $data->status = $validatedData['status'];
            $data->save();
            return response()->json(['message' => 'loan application updated', 'user' => $data], 201);
    }

    public function loan_transitioning(Request $request, $id){
        $data = user::find($id);
        if($request->status == 'processing'){
            $data->status = $request->status;
            $data->save();
            return response()->json(['message' => 'loan application in processing stage', 'user' => $data], 201);

        }elseif($request->status == 'Approved'){

            $data->status = $request->status;
            $data->save();
            return response()->json(['message' => 'loan approved', 'user' => $data], 201);
        }else{
            $data->status = "rejected";
            $data->save();
            return response()->json(['message' => 'loan application rejected', 'user' => $data], 201);
        }
    }



}
