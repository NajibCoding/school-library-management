<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\AccessLog as AccessLogModel;

class AccessLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $pathname = $request->getPathInfo();
        if (!in_array($pathname, ['/admin/access_logs', '/admin/access_logs/ajax_list', '/admin/error_logs', '/admin/error_logs/ajax_list'])) {
            $request->IDCodeAccessLog = now()->format('YmdHisu');
            $data = array();
            try {
                $input = $request->collect();
                if (isset($input['password'])) $input['password'] = "xxx";
                if (isset($input['password_confirm'])) $input['password_confirm'] = "xxx";
                if (isset($input['pass'])) $input['pass'] = "xxx";
                if (isset($input['pin'])) $input['pin'] = "xxx";
                $status = 'before_task';
                $method = $request->method();
                $content_request = json_encode($input);
                $actor = $request->user() ?? new User();
                $ip = get_client_ip();
                $user_agent = $request->header('user-agent');

                $query = json_encode($request->query());
                $url = $request->fullUrl();
                $referral_url = url()->previous();
                $data = [
                    'status' => $status,
                    'method' => $method,
                    'ip' => $ip,
                    'user_agent' => $user_agent,
                    'url' => $url,
                    'pathname' => $pathname,
                    'referral_url' => $referral_url,
                    'query' => $query,
                    'content_request' => $content_request,
                    'raw_request' => $request->getContent(),
                    'user_id' => $actor->id,
                    'user_name' => $actor->name,
                    'user_email' => $actor->email,
                    'id_task' => $request->IDCodeAccessLog,
                ];
                AccessLogModel::create($data);
            } catch (\Throwable $th) {
                Log::error("Cannot create Access Log at " . now());
                Log::error(json_encode($data));
            }
        }
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $pathname = $request->getPathInfo();
        if (!in_array($pathname, ['/admin/access_logs', '/admin/access_logs/ajax_list', '/admin/error_logs', '/admin/error_logs/ajax_list'])) {
            $data = array();
            $content_response = $response->getContent();
            if (is_array(json_decode($content_response, true))) {
                try {
                    $input = $request->collect();
                    if (isset($input['password'])) $input['password'] = "xxx";
                    if (isset($input['password_confirm'])) $input['password_confirm'] = "xxx";
                    if (isset($input['pass'])) $input['pass'] = "xxx";
                    if (isset($input['pin'])) $input['pin'] = "xxx";
                    $status = 'after_task';
                    $method = $request->method();
                    $content_request = json_encode($input);
                    $actor = $request->user() ?? new User();
                    $ip = get_client_ip();
                    $user_agent = $request->header('user-agent');
                    $query = json_encode($request->query());
                    $url = $request->fullUrl();
                    $referral_url = url()->previous();
                    $data = [
                        'status' => $status,
                        'method' => $method,
                        'ip' => $ip,
                        'user_agent' => $user_agent,
                        'url' => $url,
                        'pathname' => $pathname,
                        'referral_url' => $referral_url,
                        'query' => $query,
                        'content_request' => $content_request,
                        'raw_request' => $request->getContent(),
                        'content_response' => $content_response,
                        'user_id' => $actor->id,
                        'user_name' => $actor->name,
                        'user_email' => $actor->email,
                        'id_task' => $request->IDCodeAccessLog,
                    ];
                    AccessLogModel::create($data);
                } catch (\Throwable $th) {
                    Log::error("Cannot create Access Log at " . now());
                    Log::error(json_encode($data));
                }
            } else {
                try {
                    $input = $request->collect();
                    if (isset($input['password'])) $input['password'] = "xxx";
                    if (isset($input['password_confirm'])) $input['password_confirm'] = "xxx";
                    if (isset($input['pass'])) $input['pass'] = "xxx";
                    if (isset($input['pin'])) $input['pin'] = "xxx";
                    $status = 'after_task';
                    $method = $request->method();
                    $content_request = json_encode($input);
                    $actor = $request->user() ?? new User();
                    $ip = get_client_ip();
                    $user_agent = $request->header('user-agent');
                    $query = json_encode($request->query());
                    $url = $request->fullUrl();
                    $referral_url = url()->previous();
                    $data = [
                        'status' => $status,
                        'method' => $method,
                        'ip' => $ip,
                        'user_agent' => $user_agent,
                        'url' => $url,
                        'pathname' => $pathname,
                        'referral_url' => $referral_url,
                        'query' => $query,
                        'content_request' => $content_request,
                        'raw_request' => $request->getContent(),
                        'content_response' => preg_replace('/\s+/', ' ', $content_response),
                        'user_id' => $actor->id,
                        'user_name' => $actor->name,
                        'user_email' => $actor->email,
                        'id_task' => $request->IDCodeAccessLog,
                    ];
                    AccessLogModel::create($data);
                } catch (\Throwable $th) {
                    Log::error("Cannot create Access Log at " . now());
                    Log::error(json_encode($data));
                }
            }
        }
    }
}
