<?php
// FILE: /app/helpers/PaginatorHelper.php

class PaginatorHelper {

    public static function render($totalItems, $currentPage, $perPage, $baseUrl) {
        $totalPages = ceil($totalItems / $perPage);

        if ($totalPages <= 1) {
            return '';
        }

        $html = '<nav class="pagination">';
        $html .= '<ul class="pagination-list">';

        // Previous button
        if ($currentPage > 1) {
            $prevUrl = self::buildUrl($baseUrl, $currentPage - 1);
            $html .= '<li><a href="' . htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8') . '" class="pagination-link">&laquo; Previous</a></li>';
        }

        // Page numbers
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        if ($startPage > 1) {
            $url = self::buildUrl($baseUrl, 1);
            $html .= '<li><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="pagination-link">1</a></li>';
            if ($startPage > 2) {
                $html .= '<li><span class="pagination-ellipsis">...</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $url = self::buildUrl($baseUrl, $i);
            $activeClass = $i == $currentPage ? ' active' : '';
            $html .= '<li><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="pagination-link' . $activeClass . '">' . $i . '</a></li>';
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $html .= '<li><span class="pagination-ellipsis">...</span></li>';
            }
            $url = self::buildUrl($baseUrl, $totalPages);
            $html .= '<li><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="pagination-link">' . $totalPages . '</a></li>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $nextUrl = self::buildUrl($baseUrl, $currentPage + 1);
            $html .= '<li><a href="' . htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') . '" class="pagination-link">Next &raquo;</a></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    private static function buildUrl($baseUrl, $page) {
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        return $baseUrl . $separator . 'page=' . $page;
    }

    public static function getOffset($page, $perPage) {
        return ($page - 1) * $perPage;
    }

    public static function getCurrentPage() {
        $page = Request::get('page', 1);
        return max(1, (int) $page);
    }
}
