<?php
namespace App\Services;

use Firebase\JWT\JWT;

use App\Entity\Token;

class JwtAuth{

    public $manager;
    public $key;

    public function __construct() {
        $this->key = 'esta_es_la_key';
    }

    public function generateToken() {

        $token = [
            'sub' => $this->key,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        ];
        
        $jwt = JWT::encode($token, $this->key, 'HS256');
        return $jwt;
    }

}