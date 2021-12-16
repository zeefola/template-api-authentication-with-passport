<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CustomValidation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Repository\Auth\AuthRepository;

/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    use CustomValidation;

    /**
     * @var AuthRepository
     */
    private $auth;

    /**
     * AuthController constructor.
     * @param AuthRepository $auth
     */
    public function __construct(AuthRepository $auth)
    {
        $this->auth = $auth;
    }
}