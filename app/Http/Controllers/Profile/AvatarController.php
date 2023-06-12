<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $path = Storage::disk('public')->put('avatars', $request->file('avatar'));
        // $path = $request->file('avatar')->store('avatars', 'public');

        if ($old_avatar = $user->avatar)
        {
            Storage::disk('public')->delete($old_avatar);
        }

        $user->update(['avatar' => $path]);

        return back()->with('message', 'Avatar updated.');
    }
}
