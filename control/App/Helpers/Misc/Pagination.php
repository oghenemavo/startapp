<?php
/**
 * Project: startup
 * File: Pagination.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 21/01/2020 9:54 AM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Misc;


class Pagination {

    public $current_page;
    public $per_page;
    public $total_count;

    public function __construct($page = 1, $per_page = 20, $total_count = 0) {
        $this->current_page = (int) $page;
        $this->per_page = (int) $per_page;
        $this->total_count = (int) $total_count;
    }

    public function offset() {
        return $this->per_page * ($this->current_page - 1);
    }

    public function total_pages() {
        return ceil($this->total_count / $this->per_page);
    }

    public function previous_page() {
        $prev = $this->current_page - 1;
        return ($prev > 0) ? $prev : false;
    }

    public function next_page() {
        $next = $this->current_page + 1;
        return ($next <= $this->total_pages()) ? $next : false;
    }

    public function previous_link($url = '') {
        $link = '';
        if ($this->previous_page() != false) {
            $link .= "<a href=\"{$url}?page={$this->previous_page()}\"> Previous </a>";
        } else {
            $link .= "<span>Previous</span>";
        }
        return $link;
    }

    public function next_link($url = '') {
        $link = '';
        if ($this->next_page() != false) {
            $link .= "<a href=\"{$url}?page={$this->next_page()}\"> Next </a>";
        } else {
            $link .= "<span>Next</span>";
        }
        return $link;
    }

    public function numb_links($url = '') {
        $link = '';
        for ($i=1; $i<=$this->total_pages(); $i++) {
            if ($i == $this->current_page) {
                $link .= "<span>{$i}</span>";
            } else {
                $link .= "<a href=\"{$url}?page={$i}\">{$i}</a>";
            }
        }
        return $link;
    }

}