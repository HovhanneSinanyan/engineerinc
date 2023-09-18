<?php 

namespace App\Controllers;
use App\Models\User;
use App\Services\AuthService;
use Kernel\Exceptions\AuthException;
use Kernel\Exceptions\ValidationException;
use Kernel\Validator;

class AuthController {

   
    public function register($request) {

        $validator = new Validator($request);

        $validator->validateRequired('first_name', 'First Name is required');
        $validator->validateRequired('last_name', 'Last Name is required');
        $validator->validateRequired('email', 'email is required');
        $validator->validateEmail('email', 'Invalid email format');

        if(!$validator->isValid()) {
            $errors = $validator->getErrors();
            throw new ValidationException($errors);
        }

        $user = new User();
        $existingUser = $user->where('email', '=', $request['email'])->first();
        // var_dump($existingUser);
        if(!$existingUser) {
            $user->create([
                'email' => $request['email'],
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'password' => password_hash($request['password'], PASSWORD_DEFAULT),
            ]);
            
            return $user->save() ? "User created succesfully.": "Could not create a user.";
        } else {
            return "User with this email already exists.";
        }
    }

    public function login($request) {
        $user = new User();
        $authUser = $user->where('email', '=', $request['email'])->first();
        if($authUser) {
            $passwordMatch = password_verify($request['password'], $authUser->password);
            if($passwordMatch) {
                AuthService::login($authUser->id);
                return "{$authUser->first_name} {$authUser->last_name} ";
            }
        }
        throw new AuthException();
    }

    public function user() {
        $user = AuthService::user();
        return json_encode($user->toArray());
    }

    public function logout() {
        AuthService::logout();
        return "logged out";
    }
}