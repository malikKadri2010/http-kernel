<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DumpDataCollector;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * DumpDataCollectorTest
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        $data = new Data(array(array(123)));

        $collector = new DumpDataCollector();

        $this->assertSame('dump', $collector->getName());

        $collector->dump($data); $line = __LINE__;
        $this->assertSame(1, $collector->getDumpsCount());

        $xDump = array(
            array(
              'data' => '',
              'name' => 'DumpDataCollectorTest.php',
              'file' => __FILE__,
              'line' => $line,
              'fileExcerpt' => false,
            ),
        );

        $this->assertSame($xDump, $collector->getDumps());

        $xDump[0]['data'] = '123';
        $this->assertSame($xDump, $collector->getDumps(true));

        $this->assertStringStartsWith(
            'a:1:{i:0;a:5:{s:4:"data";O:39:"Symfony\Component\VarDumper\Cloner\Data":1:{s:45:"Symfony\Component\VarDumper\Cloner\Datadata";a:1:{i:0;a:1:{i:0;i:123;}}}s:4:"name";s:25:"DumpDataCollectorTest.php";',
            str_replace("\0", '', $collector->serialize())
        );

        $this->assertSame(0, $collector->getDumpsCount());
        $this->assertSame('a:0:{}', $collector->serialize());
    }

    public function testFlush()
    {
        $data = new Data(array(array(456)));
        $collector = new DumpDataCollector();
        $collector->dump($data); $line = __LINE__;

        ob_start();
        $collector = null;
        $this->assertSame("DumpDataCollectorTest.php on line {$line}:\n456\n", ob_get_clean());
    }
}