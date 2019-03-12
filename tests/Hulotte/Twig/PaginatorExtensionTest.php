<?php

namespace Tests\Hulotte\Twig;

use PHPUnit\Framework\TestCase;
use Hulotte\{
    Services\Paginator,
    Twig\PaginatorExtension
};

/**
 * Class PaginatorExtensionTest
 *
 * @package Tests\Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PaginatorExtensionTest extends TestCase
{
    public function testFirstLink()
    {
        $records = [
            'data1', 'data2', 'data3,'
        ];
        $paginator = new Paginator($records, 1, 3);
        $path = '/test';
        $paginatorExtension = new PaginatorExtension();

        $html = '<div class="pagination">';
        $html .= '<a href="/test?p=1">1</a>';
        $html .= '<a href="/test?p=2">2</a>';
        $html .= '<a href="/test?p=3">3</a>';
        $html .= '<a href="/test?p=2">></a>';
        $html .= '</div>';

        $this->assertEquals($html, $paginatorExtension->paginatorLinks($paginator, $path));
    }

    public function testLastLink()
    {
        $records = [
            'data1', 'data2', 'data3,'
        ];
        $paginator = new Paginator($records, 3, 3);
        $path = '/test';
        $paginatorExtension = new PaginatorExtension();

        $html = '<div class="pagination">';
        $html .= '<a href="/test?p=2"><</a>';
        $html .= '<a href="/test?p=1">1</a>';
        $html .= '<a href="/test?p=2">2</a>';
        $html .= '<a href="/test?p=3">3</a>';
        $html .= '</div>';

        $this->assertEquals($html, $paginatorExtension->paginatorLinks($paginator, $path));
    }

    public function testMiddleLink()
    {
        $records = [
            'data1', 'data2', 'data3,'
        ];
        $paginator = new Paginator($records, 2, 3);
        $path = '/test';
        $paginatorExtension = new PaginatorExtension();

        $html = '<div class="pagination">';
        $html .= '<a href="/test?p=1"><</a>';
        $html .= '<a href="/test?p=1">1</a>';
        $html .= '<a href="/test?p=2">2</a>';
        $html .= '<a href="/test?p=3">3</a>';
        $html .= '<a href="/test?p=3">></a>';
        $html .= '</div>';

        $this->assertEquals($html, $paginatorExtension->paginatorLinks($paginator, $path));
    }
}
