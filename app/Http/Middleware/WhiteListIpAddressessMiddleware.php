<?php

namespace App\Http\Middleware;

use App\Models\AdminIpModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhiteListIpAddressessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedHostDomains = [
            'agentapi-cikatech.cktch.top',
            '127.0.0.1'
        ];

        if (in_array($request->getHost(), $allowedHostDomains)) {
            return $next($request);
        }

        $ipClient = $request->ip ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ipWhiteListed = AdminIpModel::select('ip_address')->where('whitelisted', true)->get();
        $ipWL = [];
        foreach ($ipWhiteListed as $key => $ip) {
            $arrayIp = explode('.', $ip->ip_address);
            if ($arrayIp[2] == '*' && $arrayIp[3] == '*') {
                $newIp = [];
                for ($x = 0; $x <= 255; $x++) {
                    $newIp[] = $arrayIp[0] . '.' . $arrayIp[1] . '.' . $x . '.' . $x;
                }
                foreach ($newIp as $key => $ip) {
                    $arrayIpNew = explode('.', $ip);
                    for ($x = 0; $x <= 255; $x++) {
                        $newIpAddress[] = $arrayIpNew[0] . '.' . $arrayIpNew[1] . '.' . $arrayIpNew[2] . '.' . $x;
                    }
                }
            } else if ($arrayIp[2] != '*' && $arrayIp[3] == '*') {
                $newIpAddress = [];
                for ($x = 0; $x <= 255; $x++) {
                    $newIpAddress[] = $arrayIp[0] . '.' . $arrayIp[1] . '.' . $arrayIp[2] . '.' . $x;
                }
            } else {
                $newIpAddress = [$ip->ip_address];
            }
            foreach ($newIpAddress as $key => $ip) {
                $ipWL[] = $ip;
            }
        }

        $dataIpClient = explode(', ', $ipClient);
        $data = [];
        foreach ($dataIpClient as $key => $ip) {
            if (!in_array($ip, $ipWL)) {
                $data[] = false;
            } else {
                $data[] = true;
            }
        }

        if (!in_array(true, $data)) {
            // abort(403, "Your IP is Blacklisted on our site or not Whitelisted");
            return response()->json([
                'code' => 403,
                'status' => 2,
                'message' => 'Access denied!',
                'desc' => 'IP not registered or blocked',
                'your_ip' => $ipClient,
                'data' => $data,
            ], 403);
        }
        return $next($request);
    }
}
