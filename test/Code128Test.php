<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Code128;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Code128Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateTest()
    {
        $dataArr = [
            'ABCDEF'       => true,
            'abcdef'       => true,
            '01234567890'  => true,
            "\x01\x1a\x2f" => true,
            "\xab\xcd\xbd" => true,
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Code128($data);
                $this->assertTrue($info);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info);
            }
        }
    }
    
    /**
     * @test
     * @depends validateTest
     */
    public function infoTest()
    {
        $bc = new Code128("1234RRRaaafff12345\x0\xab");
        $this->assertEquals($bc->getData(), "1234RRRaaafff12345\x0\xab");
        $this->assertEquals($bc->getEncoded()->getBinary(), '11010011100101100111001000101100010111101110110001011101100010111011000101110100101100001001011000010010110000101100001001011000010010110000100100111001101011101111011101101110101110110001110101111010100001100101111011101011110111011000100100111011101101100011101011');
    }
    
    /**
     * @test
     * @depends infoTest
     */
    public function conversionTest()
    {
        $tests = [
            '123'          => '11010011100101100111001011110111011001011100100101100001100011101011',
            '1256'         => '110100111001011001110011100010110111011011101100011101011',
            '123abc'       => '11010010000100111001101100111001011001011100100101100001001000011010000101100100001101001100011101011',
            '1234abc'      => '11010011100101100111001000101100010111101110100101100001001000011010000101100100010001101100011101011',
            '1234ABC123'   => '11010011100101100111001000101100010111101110101000110001000101100010001000110100111001101100111001011001011100101011110001100011101011',
            '856bcg7854'   => '11010010000111010011001101110010011001110100100100001101000010110010011010000101110111101100001010011101011000111101110101100011101011',
            'RtGh1452CVB'  => '110100100001100010111010011110100110100010001001100001010111011110100110011101101110001010111101110100010001101110101100010001011000100010111101100011101011',
            'RtGh1CVB'     => '110100100001100010111010011110100110100010001001100001010011100110100010001101110101100010001011000111010011001100011101011',
            "\x00\x01ABcd" => '1101000010010100001100100101100001010001100010001011000101111011101000010110010000100110111000101101100011101011',
            "1245\x7fbcde" => '110100111001011001110010111011000101111011101011110100010010000110100001011001000010011010110010000100110000101100011101011',
            "aéíOU1234"    => '110100100001001011000010111101110100010001101011110111011001001000101111011101000100011010111101110100110111001000111011011011101110101110111101011001110010001011000101111010001100011101011',
            "\xab\xac\x00" => '11010010000101111011101100010010010111101110101100111001110101111010100001100110111001001100011101011',
        ];
        
        foreach ($tests as $data => &$bin) {
            $obj = new Code128($data);
            $this->assertEquals($obj->getEncoded()->getBinary(), $bin);
        }
    }
}