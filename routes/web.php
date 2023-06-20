<?php

use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use OpenAI\Laravel\Facades\OpenAI;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//AUTH
Route::middleware('auth')->group(function () {
    //PROFILE
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
        Route::post('/profile/avatar/ai', [AvatarController::class, 'generate'])->name('profile.avatar.ai');
    });

    //TICKET
    Route::prefix('ticket')->name('ticket.')->group(function () {
        Route::resource('/', TicketController::class);
    });
});

require __DIR__ . '/auth.php';

// SOCIALITE GITHUB LOGIN
Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();
    $user = User::firstOrCreate(
        ['email' => $user->email],
        [
            'name' => $user->name,
            'password' => 'admin123',
        ]
    );

    Auth::login($user);
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::resource('/ticket', TicketController::class);
});
