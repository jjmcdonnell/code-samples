<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AjaxLoginAttempts;
use Carbon\Carbon;

class AjaxLoginController extends Controller {

    public function loginUser(Request $request) {
        $email = $request->email;
        $password = $request->password;
        $result[] = null;
        $error = false;
        $ipNodeBlock = $this->checkBannedIpThrottle(request()->ip());
        if ($ipNodeBlock['blocked'] == false) {
            try {
                if (Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
                    if ($this->authorizedRootIp(request()->ip(), $email)) {
                        $result = [
                            'status' => true,
                            'message' => 'Login Successful'
                        ];
                        $log_message = "Ajax Login Success for UserID: " . Auth::id();
                        Log::channel('slack')->info($log_message);
                        $renderlogic_admin = \App\User::find(1);
                        $renderlogic_admin->notify(new \App\Notifications\LoginNotification($log_message));
                    } else {
                        Auth::logout();
                        $result = [
                            'status' => false,
                            'message' => 'Invalid Credentials/Permissions.'
                        ];
                        Log::channel('slack')->info("Ajax Login - failed root ip check - from IP: " . request()->ip() . " for USERID: " . Auth::id());
                        $renderlogic_admin = \App\User::find(1);
                        $renderlogic_admin->notify(new \App\Notifications\AjaxRootFailureNotification("Ajax Login - failed root ip check - from IP: " . request()->ip() . " for USERID: " . Auth::id()));
                    }
                } else {
                    $throttle_response = $this->customAjaxThrottle($email, $ipNodeBlock['logged_ip_attempts']);
                    $result['status'] = $throttle_response['status'];
                    $result['message'] = $throttle_response['message'];
                    Log::critical($result['message']);
                }
            } catch (\Exception $e) {
                $error = "other error: " . $e;
            }
            if ($error) {
                $result['status'] = false;
                $result['message'] = $e;
                Log::channel('slack')->critical("Authentication Database Attempt Error" . $e);
            }
        } else {
            $result['status'] = false;
            $result['message'] = "Access Blocked/Restricted. Contact Support.";
            Log::channel('slack')->info("Login Access Attempt From Blocked Node: " . request()->ip());
            $renderlogic_admin = \App\User::find(1);
            $renderlogic_admin->notify(new \App\Notifications\AjaxIpLockNotification('Login Access Attempt From Blocked Node: ' . request()->ip()));
        }
        return response()->json($result);
    }

    private function customAjaxThrottle($credential_used, $previous_ip_attempts) {
        $attempts_allowed = config('auth.throttlers.default.attempts');
        $error = false;
        $result[] = null;
        if ($error == false) {
            try {
                $previous_attempts = AjaxLoginAttempts::where('credential_used', $credential_used)->count();
            } catch (\Illuminate\Database\QueryException $e) {
                $error = "database error: " . $e;
            } catch (\Exception $e) {
                $error = "other error: " . $e;
            }
        }
        if ($error == false) {
            try {
                $credential_count = \App\User::where('email', $credential_used)->count();
            } catch (\Illuminate\Database\QueryException $e) {
                $error = "database error: " . $e;
            } catch (\Exception $e) {
                $error = "other error: " . $e;
            }
        }
        if ($error == false) {
            /* if hit lockout throttle - and account exists flag account otherwise just block that user and ip */
            if ($credential_count == (int) 1) {
                /* they're using an actual registered account credential - flag the account for loggin and user notificaiton */
            }
            /* apply the attempt allowed constraint */
            if ($previous_attempts >= $attempts_allowed) {
                $error = "LOCKED! Max attempts of " . $previous_attempts . " used of " . $attempts_allowed . " allotted reached! contact support!";
                Log::channel('slack')->info($credential_used . " - Credentials Attempt Locked Reach - " . $error);
                $renderlogic_admin = \App\User::find(1);
                $renderlogic_admin->notify(new \App\Notifications\AjaxCredentialLockNotification($credential_used . " - " . $error));
            } else if ($previous_ip_attempts >= 5) {
                $error = "LOCKED! Max attempts of " . $previous_ip_attempts . " used of " . $attempts_allowed . " allotted reached! contact support!";
                Log::channel('slack')->info($credential_used . " - IP Attempt Lock Reached - " . $error);
                $renderlogic_admin = \App\User::find(1);
                $renderlogic_admin->notify(new \App\Notifications\AjaxIpLockNotification($credential_used . " - " . $error));
            } else {
                $previous_attempts = $previous_attempts + 1;
                $previous_ip_attempts = $previous_ip_attempts + 1;
                $error = "WARNING! login attempts: " . $previous_ip_attempts . " used of " . $attempts_allowed . " allotted before attempt lock out!";
                try {
                    $new_attempt = new AjaxLoginAttempts;
                    $new_attempt->credential_used = $credential_used;
                    $new_attempt->ip_used = request()->ip();
                    $new_attempt->created_at = Carbon::now()->timestamp;
                    $new_attempt->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    $error = "database error: " . $e;
                    Log::channel('slack')->critical($error);
                } catch (\Exception $e) {
                    $error = "other error: " . $e;
                    Log::channel('slack')->critical($error);
                }
                Log::channel('slack')->info($credential_used . " - " . $error);
                $renderlogic_admin = \App\User::find(1);
                $renderlogic_admin->notify(new \App\Notifications\AjaxFailNotification($credential_used . " - " . $error));
            }
        }
        if ($error) {
            $result['status'] = false;
            $result['message'] = $error;
        } else {
            $result['status'] = true;
        }
        return $result;
    }

    private function checkBannedIpThrottle($current_ip) {
        $result = array();
        $attempts_allowed = config('auth.throttlers.default.attempts');
        $previous_ip_attempts = AjaxLoginAttempts::where('ip_used', $current_ip)->count();
        if ($previous_ip_attempts >= $attempts_allowed) {
            $result['blocked'] = true;
        } else {
            $result['blocked'] = false;
        }
        $result['logged_ip_attempts'] = $previous_ip_attempts;
        return $result;
    }

    private function authorizedRootIp($ip, $email) {
        if ($email == "john@renderlogic.com") {
            if ($ip == "192.168.10.1") {
                return true;
            } else {
                /* throttle invalid root ip attempts */
                try {
                    $new_attempt = new AjaxLoginAttempts;
                    $new_attempt->credential_used = $email;
                    $new_attempt->ip_used = request()->ip();
                    $new_attempt->created_at = Carbon::now()->timestamp;
                    $new_attempt->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    $error = "database error: " . $e;
                    Log::channel('slack')->critical($error);
                } catch (\Exception $e) {
                    $error = "other error: " . $e;
                    Log::channel('slack')->critical($error);
                }
                return false;
            }
        } else {
            return true;
        }
    }

}
