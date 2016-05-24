<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractChecksumBarcode,
    Zeus\Barcode\FixedLengthInterface,
    Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a EAN13 barcode standard.
 * 
 * Supports 13 numeric chars and the last digit it's the checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class Ean13 extends AbstractChecksumBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * Product field
     * 
     */
    const PRODUCT = 7;

    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [0, 0, 0, 0, 0, 0],
        '1' => [0, 0, 1, 0, 1, 1],
        '2' => [0, 0, 1, 1, 0, 1],
        '3' => [0, 0, 1, 1, 1, 0],
        '4' => [0, 1, 0, 0, 1, 1],
        '5' => [0, 1, 1, 0, 0, 1],
        '6' => [0, 1, 1, 1, 0, 0],
        '7' => [0, 1, 0, 1, 0, 1],
        '8' => [0, 1, 0, 1, 1, 0],
        '9' => [0, 1, 1, 0, 1, 0],
    ];

    /**
     * 
     * @param string $bin
     * @return Ean13 Or its subclasses...
     * @throws Ean13Exception
     */
    public static function fromBinary($bin)
    {
        if (\preg_match('/^101([01]{42})01010([01]{42})101$/', $bin, $match)) {
            $bin = $match[1] . $match[2];
        }
        else {
            throw new Ean13Exception('Invalid binary string!');
        }
        
        $bin  = \str_split($bin, 7);
        $data = '';
        $ptab = [];
        
        foreach ($bin as $i => &$binChar) {
            $p     = $i < 6 ? [0, 1] : [2];
            $data .= self::decode($binChar, $ptab, $p);
        }
        
        $p = \array_search($ptab, self::$parityTable);
        if ($p !== false) {
            $data = $p . $data;
            $class = \get_called_class();
            return new $class($data);
        }
        throw new Ean13Exception('Invalid binary encode');
    }

    /**
     * Separates the first digit
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $data = $this->getData();
        return $data{0} . ' ' . \substr($data, 1);
    }
    
    /**
     * Returns 13.
     * 
     * @return int
     */
    public function getLength()
    {
        return 13;
    }
    
    /**
     * Returns the product code.
     * 
     * @return string
     */
    public function getProductCode()
    {
        return $this->getDataPart(self::PRODUCT, 5);
    }
    
    /**
     * Creates a new instance with another product code.
     * 
     * @param string|int $code
     * @return Ean13
     */
    public function withProductCode($code)
    {
        $data = $this->withDataPart($code, self::PRODUCT, 5);
        return new self($data, false);
    }
    
    /**
     * Checks if barcode is compatible with UPC-A.
     * 
     * @return bool
     */
    public function isUpcaCompatible()
    {
        return $this->data{0} == '0';
    }
    
    /**
     * Converts this barcode to a UPC-A barcode, if compatible. Otherwise,
     * a exception will be throw.
     * 
     * @return Upca
     * @exception Ean13Exception
     */
    public function toUpca()
    {
        if ($this->isUpcaCompatible()) {
            return new Upca(\substr($this->data, 1));
        }
        throw new Ean13Exception('Uncompatible UPC-A barcode!');
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoded   = '';
        $parityTab =& self::$parityTable[$data{0}];
        
        for ($i = 1; $i < 13; $i++) {
            $parity   = $i <= 6 ? $parityTab[$i - 1] : 2;
            $encoded .= self::$encodingTable[$data{$i}][$parity];
        }
        
        $encoder->addBinary('101', 1.3)
                ->addBinary(\substr($encoded, 0, 42))
                ->addBinary('01010', 1.3)
                ->addBinary(\substr($encoded, 42))
                ->addBinary('101', 1.3);
    }
}

/**
 * Ean13 exception
 */
class Ean13Exception extends Exception {}
