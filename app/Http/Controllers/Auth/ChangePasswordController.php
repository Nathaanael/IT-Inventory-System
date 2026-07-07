<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed', // expects password_confirmation field
                // Custom rule for 2 of the 4 constraints: uppercase, lowercase, numbers, special characters
                function ($attribute, $value, $fail) {
                    $hasUpper = preg_match('/[A-Z]/', $value);
                    $hasLower = preg_match('/[a-z]/', $value);
                    $hasNumeric = preg_match('/[0-9]/', $value);
                    $hasSpecial = preg_match('/[!@#$%^&*(),.?\':{}|<>~\\/-]/', $value);

                    $passed = (int)$hasUpper + (int)$hasLower + (int)$hasNumeric + (int)$hasSpecial;

                    if ($passed < 2) {
                        $fail('The :attribute must meet at least two requirements: uppercase, lowercase, number, special character.');
                    }
                }
            ],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah!');
    }
}
