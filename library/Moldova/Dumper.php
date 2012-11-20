<?php

/**
 * Colorful Dumper (part of Lotos Framework)
 *
 * Copyright (c) 2005-2010 Artur Graniszewski (aargoth@boo.pl) 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * - Neither the name of the Lotos Framework nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Library
 * @package    Lotos
 * @subpackage Dumper
 * @copyright  Copyright (c) 2005-2010 Artur Graniszewski (aargoth@boo.pl)
 * @license    New BSD License
 * @version    $Id$
 */
class Moldova_Dumper
{
    /**
     * Background CSS color, for example: "#000000" or "black"
     * 
     * @var string
     */
    public static $backgroundColor = 'white';
    
    /**
     * Braces CSS color, for example: "#000000" or "black"
     * 
     * @var string
     */
    public static $bracesColor = 'red';
    
    /**
     * Data type CSS color, for example: "#000000" or "black"
     * 
     * @var string
     */
    public static $typeColor = 'black';
    
    /**
     * Numeric value CSS color, for example: "#000000" or "black"
     * 
     * @var string
     */
    public static $numericValueColor = 'green';
    
    /**
     * put your comment there...
     * 
     * @var string
     */
    public static $stringsValueColor = 'blue';
    
    /**
     * String value CSS color, for example: "#000000" or "black"
     * 
     * @var string
     */
    public static $indexColor = '#FF8000';    
    
    /**
     * Sets custom color theme in one static method call.
     * 
     * @param string $backgroundColor Background CSS color, for example: "#000000" or "black"
     * @param string $indexColor Index CSS color, for example: "#000000" or "black"
     * @param string $bracesColor Braces CSS color, for example: "#000000" or "black"
     * @param string $typeColor Data type CSS color, for example: "#000000" or "black"
     * @param string $numericValueColor Numeric value CSS color, for example: "#000000" or "black"
     * @param string $stringsValueColor String value CSS color, for example: "#000000" or "black"
     * @return void
     */
    public static function setColors($backgroundColor, $indexColor, $bracesColor, $typeColor, $numericValueColor, $stringsValueColor) {
        self::$indexColor = $indexColor;
        self::$backgroundColor = $backgroundColor;
        self::$bracesColor = $bracesColor;
        self::$typeColor = $typeColor;
        self::$numericValueColor = $numericValueColor;
        self::$stringsValueColor = $stringsValueColor;
    }
    
    /**
     * Displays structured information about one or more expressions that includes its type and value. 
     * 
     * @param mixed $str Structure to display.
     * @return void
     */
    public static function dump($str) {
        echo '<pre style="color: '.self::$bracesColor.'; background-color: '.self::$backgroundColor.';" >';
        $header = "\"<span style=\\\"color: ".self::$indexColor."\\\">\$matches[1]</span><span style=\\\"color: ".self::$bracesColor."\\\">\$matches[2]</span><span style=\\\"color: ".self::$typeColor."\\\">\$matches[3]</span>\"";
        $function = create_function('$matches', '
            $count = count($matches);
            if($count == 7 && $matches[4] === $matches[5] && $matches[6]) {
                $ret = '.$header.'."<span style=\"color: '.self::$numericValueColor.'\">$matches[5]</span>";
            } else if($count == 7) {
                $ret = '.$header.'."<span style=\"color: '.self::$numericValueColor.'\">$matches[5]</span><span style=\"color: '.self::$stringsValueColor.'\">$matches[6]</span>";
            } else if($count == 4){
                $ret = '.$header.';
            } else if($count == 10) {
                $ret = '.$header.'."<span style=\"color: '.self::$numericValueColor.'\">$matches[6]</span><span style=\"color: '.self::$typeColor.'\">$matches[8]</span><span style=\"color: '.self::$numericValueColor.'\">$matches[9]</span>";
            } else if($count == 11) {
                // strings
                $ret = '.$header.'."<span style=\"color: '.self::$numericValueColor.'\">$matches[5]</span><span style=\"color: '.self::$stringsValueColor.'\">$matches[10]</span>";
            } else {
                $ret = $matches[0];
            }
            return $ret;
            
        ');
        ob_start();
        var_dump($str);
        $str = '["commentid"]=>'."\n ".ob_get_clean();
        $str = preg_replace_callback('~(\[[^\]]+\]|[\d]+)(=>\n\s+)([a-zA-Z_\d]+)(((\([^)]+\))((#[\d]+)(\s\([\d]+\)))?)(\s"[^"]*")?)?~', $function, $str);

        $str = ltrim(substr($str, strpos($str, "\n ")));
        echo $str;
        echo "</pre>";
    }    
}

/**
 * Displays structured information about one or more expressions that includes its type and value. 
 * 
 * @param mixed $str Structure to display.
 * @return void
 */
function dump($args) {
    $args = func_get_args();
    foreach($args as $arg) {
        Moldova_Dumper::dump($arg);
    }
}

