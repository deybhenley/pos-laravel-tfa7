<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')],
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user, AvatarService $avatars): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user)],
            'full_name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['avatar']);
        $oldAvatar = $user->avatar;
        $newAvatar = null;

        if ($request->hasFile('avatar')) {
            try {
                $newAvatar = $avatars->store($request->file('avatar'));
                $data['avatar'] = $newAvatar;
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withErrors(['avatar' => 'The image could not be prepared or saved. Please try another JPEG, PNG, or WebP image.'])
                    ->withInput($request->only('username', 'full_name'));
            }
        }

        try {
            $user->update($data);
        } catch (Throwable $exception) {
            $avatars->delete($newAvatar);
            throw $exception;
        }

        if ($newAvatar) {
            $avatars->delete($oldAvatar);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
