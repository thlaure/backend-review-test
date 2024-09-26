<?php

namespace App\Dto;

class ActorData
{
    public function __construct(
        public string $id,
        public string $login,
        public string $url,
        public string $avatarUrl
    ) {
    }
}
