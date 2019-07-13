<?php
function generate_password($length) {
    $chars     = '0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+';
    $password  = '';
    $num_chars = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $random  = rand(0, $num_chars - 1);
        $check   = substr($chars, $random, 1);
        $pattern = '@' . $check . '@';

        if (preg_match($pattern, $password)) {
            $i--;
        } else {
            $password.=$check;
        }
    }
    return $password;
}
