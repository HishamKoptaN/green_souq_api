<?php

function successRes($data = null, $code = 200)
{
    return response()->json(
        $data,
        $code,
    );
}
function failureRes(
    $message = 'حدث خطأ ما',
    $status = 500,
    $errors = null,
    $errorCode = null
) {
    if (is_array($errors)) {
        $errors = implode(', ', array_flatten($errors));
    }

    return response()->json([
        'message' => $message,
        'errors' => $errors,
        'error_code' => $errorCode,
    ], $status);
}
function validationErrorRes($errors, $message = 'البيانات المدخلة غير صالحة', $status = 422)
{
    if (is_array($errors)) {
        $errors = implode(', ', array_flatten($errors));
    }

    return response()->json(
        [
            'message' => $message,
            'error' => $errors,
        ],
        $status,
    );
}
