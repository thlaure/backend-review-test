<?php

namespace App\Dto;

class RepoData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $url
    ) {
    }
}
