<?php

namespace App\Dtos\Utils;

use App\Dtos\BaseDto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PaginationDto extends BaseDto
{

    public int $current_page;
    public int $per_page;
    public bool $has_more;
    public int $items_count;
    public int $total_items;
    public int $total_pages;

    public function __construct(Paginator|LengthAwarePaginator|null $paginator)
    {
        if (is_null($paginator)) {
            $this->current_page = 0;
            $this->per_page = 0;
            $this->items_count = 0;
            $this->total_items = 0;
            $this->total_pages = 0;
            $this->has_more = false;
            return;
        }
        $this->current_page = $paginator->currentPage();
        $this->per_page = $paginator->perPage();
        $this->items_count = $paginator->count();
        $this->total_items = $paginator->total();
        $this->total_pages = $paginator->lastPage();
        $this->has_more = $paginator->hasMorePages();
    }
}
