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

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function registerUser(Request $request): JsonResponse
    {
        $email = 'bail|required|email';
        if (App::environment('production')) {
            $email = 'bail|required|email:rfc,dns';
        }
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'bail|required|string',
            'phone_number' => 'bail|required',
            'email' => $email,
            'password' => 'bail|required|min:8',
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(['error' => true, 'msg' => $messages]);
        }

        $validatedPhoneNumber = $this->validatePhoneNumber($input['phone_number']);

        if (!$validatedPhoneNumber['valid']) {

            return response()->json([
                'error' => true,
                'msg' => [
                    'phone_number' => array(["Not a valid phone number, Phone number should be in the format '08*********'"])
                ]
            ]);
        }

        $input['phone_number'] = $validatedPhoneNumber['phone_number'];

        return response()->json($this->auth->registerUser($input));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmAccount(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'bail|required|email',
            'confirm_code' => 'bail|required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(['error' => true, 'msg' => $messages]);
        }

        return response()->json($this->auth->confirmAccount($input));
    }
}