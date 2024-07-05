<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\JsonResponse;
use Exception;

class CheckJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if token is provided in the request
            if (!$request->header('Authorization')) {
                return new JsonResponse(['error' => 'Token not provided'], 400);
            }

            // Parse the token from the header
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Attempt to authenticate the user via the token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return new JsonResponse(['error' => 'Unauthorized'], 401);
            }
        } catch (TokenExpiredException $e) {
            return new JsonResponse(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return new JsonResponse(['error' => 'Token is invalid'], 401);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Token is not provided'], 400);
        }

        return $next($request);
    }
}
