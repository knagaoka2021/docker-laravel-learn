<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @return View
     */
    public function showLogin()
    {
        return view('login.login_form');
    }
    /**
     * @param App\Http\Requests\LoginFormRequest
     */
    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // ログイン認証 成功
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ルート名'home'を参照
            return redirect()->route('home')->with('success', 'ログイン成功しました!');
        }
        // ログイン認証 失敗
        return back()->withErrors([
            // セッションキー,値
            'danger' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }
    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('danger', 'ログアウトしました!');
    }
}
