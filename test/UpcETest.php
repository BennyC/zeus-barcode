<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\UpcE;

/**
 * 
 * @author Rafael M. Salvioni
 */
class UpcETest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '01240136' => [true, true],
            '01234565' => [true, true],
            '1234565'  => [true, true], // zero pad
            '1240136'  => [true, true], // zero pad
            '2124013'  => [false, false], // wrong system digit
            '01240135' => [true, false], // wrong digit
            '8'        => [true, false],
            ''         => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new UpcE($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertEquals($bc->toUpcA()->toUpcE()->getData($info[1]), $data);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
