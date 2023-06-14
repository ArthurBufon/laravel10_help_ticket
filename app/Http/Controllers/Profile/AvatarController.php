<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

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

    public function generate(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $result = OpenAI::images()->create([
            "prompt" => 'create avatar for user with cool style animated',
            'n'      => 1,
            'size'   => "256x256",
        ]);
        
        $contents = file_get_contents($result->data[0]->url);

        $filename = Str::random(25);

        if ($old_avatar = $user->avatar)
        {
            Storage::disk('public')->delete($old_avatar);
        }
        
        Storage::disk('public')->put("avatars/$filename.jpg", $contents);

        $user->update(['avatar' => "avatars/$filename.jpg"]);

        return redirect(route('profile.edit'))->with('message', 'Avatar is updated.');
    }
}
