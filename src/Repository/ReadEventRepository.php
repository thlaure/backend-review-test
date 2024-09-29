<?php

namespace App\Repository;

use App\Dto\SearchInput;

interface ReadEventRepository
{
    public function countAll(SearchInput $searchInput): int;

    /**
     * @return array<string|int, mixed>
     */
    public function countByType(SearchInput $searchInput): array;

    public function statsByTypePerHour(SearchInput $searchInput): array;

    public function getLatest(SearchInput $searchInput): array;

    public function exist(int $id): bool;
}
