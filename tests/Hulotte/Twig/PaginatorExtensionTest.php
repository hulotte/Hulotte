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
 * @coversDefaultClass \Hulotte\Twig\PaginatorExtension
 */
class PaginatorExtensionTest extends TestCase
{
    /**
     * @covers ::paginatorLinks
     */
    public function testFirstLink(): void
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

    /**
     * @covers ::paginatorLinks
     */
    public function testLastLink(): void
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

    /**
     * @covers ::paginatorLinks
     */
    public function testMiddleLink(): void
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
