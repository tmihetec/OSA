<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        /* ja */
            if ($this->isHttpException($e)) {
                return $this->renderHttpExceptionView($e);
            }

            if ($e instanceof CustomException) {
                return response()->view('errors.custom', [], 500);
            }

            if ($e instanceof \Illuminate\Session\TokenMismatchException){
            //Redirect to login form if session expires
            //    return \Redirect::back()->withInput()->with('errors', 'Your session was expired');
            return redirect($request->fullUrl())->with('csrf_error',"Opps! Seems you couldn't submit form for a longtime. Please try again");
             }

        /* */




        return parent::render($request, $e);
    }


//http://blog.dannyweeks.com/web-dev/handling-errors-in-laravel-5-1

    /**
     * Render the error view that best fits that status code.
     * 
     * @param Exception $e
     * @return \Illuminate\Http\Response
     */
    public function renderHttpExceptionView(Exception $e)
    {
        $status = $e->getStatusCode();
        
        if (view()->exists("errors.{$status}")) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        }
 
        return response()->view("errors.default", ['exception' => $e], $status);
 
    }


}

