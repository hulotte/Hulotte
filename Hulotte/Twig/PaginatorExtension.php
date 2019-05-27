<?php

namespace Hulotte\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};
use Hulotte\Services\Paginator;

/**
 * Class PaginatorExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PaginatorExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('paginator_links', [$this, 'paginatorLinks'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Return pagination links
     * @param Paginator $paginator
     * @param string $path
     * @return string
     */
    public function paginatorLinks(Paginator $paginator, string $path)
    {
        $html = '';
        
        if ($paginator->totalPages > 1) {
            $html = '<div class="pagination">';

            if ($paginator->currentPage > 1) {
                $previousNbr = $paginator->currentPage - 1;
                $previousPage = $path . '?p=' . $previousNbr;
                $html .= '<a href="' . $previousPage . '"><</a>';
            }
    
            for ($i = 1; $i <= $paginator->totalPages; $i++) {
                $link = $path . '?p=' . $i;
                $html .= '<a href="' . $link . '">' . $i . '</a>';
            }
    
            if ($paginator->currentPage < $paginator->totalPages) {
                $nextNbr = $paginator->currentPage + 1;
                $nextPage = $path . '?p=' . $nextNbr;
                $html .= '<a href="' . $nextPage . '">></a>';
            }
    
            $html .= '</div>';
        }

        return $html;
    }
}
