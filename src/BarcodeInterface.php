<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Renderer\RendererInterface;

/**
 * Identifies a barcode object.
 * 
 * @author Rafael M. Salvioni
 */
interface BarcodeInterface
{
    /**
     * Check if a barcode data is valid. If data has a checksum digit is given,
     * it will be checked too.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    public static function check($data, $hasChecksum = true);

    /**
     * Constructor should be accepts a barcode data and, if a data is invalid,
     * a exception will should throw.
     * 
     * If $hasChecksum was given, the checksum digit of data should be checked
     * too. Otherwise, the class will should calculate the checksum.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @throws Exception
     */
    public function __construct($data, $hasChecksum = true);
    
    /**
     * Returns the barcode data, with or without checksum, if has one.
     * 
     * @param bool $withChecksum
     * @return string
     */
    public function getData($withChecksum = false);
    
    /**
     * Returns a subpart of barcode data.
     * 
     * @param int $start
     * @param int $length
     * @return string
     */
    public function getDataPart($start, $length = null);
    
    /**
     * Makes a new barcode instance replacing a part of current data for another.
     * 
     * $value will be padded with left zeros if its length is less than $length.
     * If $value length is greater than $length, a exception will be throw.
     * 
     * @param string $part
     * @param int $start
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function withDataPart($part, $start, $length);

    /**
     * Returns the barcode checksum, if has one.
     * 
     * @return int
     */
    public function getChecksum();
    
    /**
     * Returns the barcode data encoded with 0 or 1. "0" represents a white
     * bar and "1" a black bar.
     * 
     * @return string
     */
    public function getEncoded();
    
    /**
     * Returns the barcode data to be printed.
     * 
     * @return string
     */
    public function getPrintableData();

    /**
     * Draw the barcode on a renderer.
     * 
     * Returns the own renderer.
     * 
     * @param RendererInterface $renderer
     * @return RendererInterface
     */
    public function render(RendererInterface $renderer);
}
