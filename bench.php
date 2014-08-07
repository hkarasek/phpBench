<?php

/*
##########################################################################
#                      PHP Benchmark Performance Script mk2              #
#                                                                        #
#  Author      : Jan Karásek                                             #
#  Company     : Hoodlabs (Czech Republic), Shean (Czech Republic)       #
#  Date        : August 2, 2014                                          #
#  version     : 2.0.0                                                   #
#  License     : Creative Commons CC-BY license                          #
#  Website     : http://hkar.eu                                          #
#                                                                        #
#                                                                        #
#  forked from:                                                          #
#  Author      : Alessandro Torrisi                                      #
#  Company     : Code24 BV, The Netherlands                              #
#  Date        : July 31, 2010                                           #
#  License     : Creative Commons CC-BY license                          #
#  Website     : http://www.php-benchmark-script.com                     #
#                                                                        #
##########################################################################
*/

class Bench
{
    private static function test_Math($count = 14000)
    {
        $time_start = microtime(true);
        $mathFunctions = array("abs", "acos", "asin", "atan", "bindec", "floor", "exp", "sin", "tan", "pi", "is_finite", "is_nan", "sqrt");
        foreach ($mathFunctions as $key => $function) {
            if (!function_exists($function)) unset($mathFunctions[$key]);
        }
        for ($i = 0; $i < $count; $i++) {
            foreach ($mathFunctions as $function) {
                $r = call_user_func_array($function, array($i));
            }
        }
        return number_format(microtime(true) - $time_start, 3);
    }


    private static function test_StringManipulation($count = 13000)
    {
        $time_start = microtime(true);
        $stringFunctions = array("addslashes", "chunk_split", "metaphone", "strip_tags", "md5", "sha1", "strtoupper", "strtolower", "strrev", "strlen", "soundex", "ord");
        foreach ($stringFunctions as $key => $function) {
            if (!function_exists($function)) unset($stringFunctions[$key]);
        }
        $string = "the quick brown fox jumps over the lazy dog";
        for ($i = 0; $i < $count; $i++) {
            foreach ($stringFunctions as $function) {
                $r = call_user_func_array($function, array($string));
            }
        }
        return number_format(microtime(true) - $time_start, 3);
    }


    private static function test_Loops($count = 1900000)
    {
        $time_start = microtime(true);
        for ($i = 0; $i < $count; ++$i) ;
        $i = 0;
        while ($i < $count) ++$i;
        return number_format(microtime(true) - $time_start, 3);
    }


    private static function test_IfElse($count = 900000)
    {
        $time_start = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            if ($i == -1) {
            } elseif ($i == -2) {
            } else if ($i == -3) {
            }
        }
        return number_format(microtime(true) - $time_start, 3);
    }

    private static function test_arrayManipulation($count = 1000000)
    {
        $data = [];
        $time_start = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $data[] = $i;
            unset($data[$i - 1]);
        }
        return number_format(microtime(true) - $time_start, 3);
    }

    private static function test_fileWriteAndRead($count = 10000)
    {
        $file = sha1('test' . microtime(true)) . '.txt';
        $time_start = microtime(true);

        for ($i = 0; $i < $count; $i++) {
            file_put_contents($file, $i);
            file_get_contents($file);
        }
        unlink($file);
        return number_format(microtime(true) - $time_start, 3);
    }

    private static function test_hashSHA1($count = 1000000)
    {
        $time_start = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            sha1('benchmark');
        }
        return number_format(microtime(true) - $time_start, 3);
    }

    private static function test_hashMD5($count = 1000000)
    {
        $time_start = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            md5('benchmark');
        }
        return number_format(microtime(true) - $time_start, 3);
    }

    public static function run()
    {
        $result = Bench::controller();
        if (isset($_GET['api']) and $_GET['api'] == true) {
            echo(Bench::jsonResponse($result));
        } else {
            echo(Bench::htmlResponse($result));
        }
    }

    private static function controller()
    {
        $total = 0;
        $methods = get_class_methods('Bench');
        $line = str_pad("-", 38, "-");
        $return = [];
        foreach ($methods as $method) {
            if (preg_match('/^test_/', $method)) {
                $total += $result = self::$method();
                $return[$method] = $result;
            }
        }
        $return['total'] = $total;


        return $return;
    }

    private static function jsonResponse($data)
    {
        return (json_encode(['status' => 200, 'server' => (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '?') . '@' . (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '?'), 'data' => $data]));
    }

    private static function htmlResponse($data)
    {
        $html = '
        <html>
            <body>
                <table>
                   <caption>Results</caption>
                   <thead>
                      <tr>
                         <th>method</th>
                         <th>time</th>
                      </tr>
                   </thead>
                   <tbody>';
        foreach ($data as $method => $time){
            $html .= "<tr>
                         <th>$method</th>
                         <td>$time</td>
                      </tr>";
        }

        $html .= '
                   </tbody>
                </table>
            </body>
        </html>
        ';
        return $html;
    }
}

Bench::run();