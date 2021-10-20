<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    public function __construct(User $user){
        $this->user = $user;
    }

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

        // アカウントロック対象ユーザは弾く
        $user = $this->user->getUserByEmail($credentials['email']);

        if (!is_null($user)) {
            // アカウントロック判定
            if ($this->user->isAccountLocked($user)) {
                return back()->withErrors([
                    // セッションキー,値
                    'danger' => 'このアカウントはロックされています。',
                ]);
            }
            // ログイン認証 成功
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                // エラーカウントを0にする
                $this->user->resetErrorCount($user);

                // ルート名'home'を参照
                return redirect()->route('home')->with('success', 'ログイン成功しました!');
            }
            // ログイン認証 失敗
            // エラーカウントを1増やす
            $user->error_count = $this->user->addErrorCount($user->error_count);

            // エラーカウントが6以上の場合はアカウントロックする
            if ($this->user->lockAccount($user)) {
                return back()->withErrors([
                    // セッションキー,値
                    'danger' => 'アカウントはロックされました。解除したい場合は運営者に問い合わせてください',
                ]);
            }
            $user->save();
        }

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
