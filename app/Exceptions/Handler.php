<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Exception $e) {
            if ($e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('/');
            };

            if ($e->getPrevious() instanceof \Illuminate\Session\HttpResponseException) {
                return redirect()->route('/');
            };

            if ($e->getPrevious() instanceof \Illuminate\Session\QueryException) {
                return redirect()->route('/');
            };

            $this->renderable(function (NotFoundHttpException $e, $request) {
                return redirect()->route('/');
            });

            // Redirect server errors to the login page
        if ($this->isHttpException($exception) && $exception->getStatusCode() >= 500) {
            return redirect()->route('/');
        }
        if ($this->isHttpException($exception) && $exception->getStatusCode() >= 419) {
            return redirect()->route('/');
        }
            
        });
    }

    
}
