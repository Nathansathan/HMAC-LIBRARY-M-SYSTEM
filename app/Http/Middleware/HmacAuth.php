<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class HmacAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $publicKey = $request->header('X-PUBLIC-KEY');
        $signature = $request->header('X-SIGNATURE') ?: $request->header('X-HMAC-Signature');
        $timestamp = $request->header('X-TIMESTAMP');

        if (!$publicKey || !$signature || !$timestamp) {
            return response()->json([
                'error' => 'Unauthorized - Missing headers',
                'received' => [
                    'X-PUBLIC-KEY' => $publicKey,
                    'X-SIGNATURE' => $request->header('X-SIGNATURE'),
                    'X-HMAC-Signature' => $request->header('X-HMAC-Signature'),
                    'X-TIMESTAMP' => $timestamp,
                ]
            ], 401);
        }

        if (!ctype_digit((string) $timestamp) || abs(time() - (int) $timestamp) > 300) {
            return response()->json([
                'error' => 'Request Expired',
                'server_time' => time(),
                'received_timestamp' => $timestamp,
            ], 401);
        }

        $user = User::where('public_key', $publicKey)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid Public Key'
            ], 401);
        }

        $body = $request->getContent() ?: '';
        $stringToSign = $request->method() . $request->fullUrl() . $body . $timestamp;
        $generatedSignature = hash_hmac('sha256', $stringToSign, $user->secret_key);

        if (!hash_equals($generatedSignature, $signature)) {
            return response()->json([
                'error' => 'Invalid Signature',
                'debug' => [
                    'string_to_sign' => $stringToSign,
                    'generated_signature' => $generatedSignature,
                    'received_signature' => $signature,
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'body' => $body,
                    'timestamp' => $timestamp,
                ]
            ], 401);
        }

        return $next($request);
    }
}