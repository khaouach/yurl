<?php
/**
 * @package		PWFramework
 * @author    	Pageworks http://www.pageworks.nl
 * @copyright	Copyright (c) 2006 - 2010 Pageworks. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

if (!(defined('_VALID_MOS') || defined('_JEXEC'))) {
    die('Restricted access');
}

/**
 * functie voor debug doeleinde, niet gebruiken in productie omgeving!
 * @author Alex Jonk
 */
function sysout($obj, $die = false) {
    if (is_object($obj) || is_array($obj)) {
        $outp = print_r($obj, true);
    } else {
        $outp = $obj;
    }
    printf("<pre>%s</pre>", $outp);
    if ($die) {
        $callers = debug_backtrace();
        echo "died on method/function : " . $callers[1]['function'];
        die();
    }
}

class DateHelper {

    public static $DATE_FORMAT = "d-m-Y";
//   public static $DATE_FORMAT_LONG = "%a d m Y";
    public static $DATE_FORMAT_SQL = "Y-m-d";
    public static $DATETIME_FORMAT = "d-m-Y H:i:s";
    public static $DATETIME_FORMAT_SQL = "Y-m-d H:i:s";
    public static $DATETIME_FORMAT_HRS_MIN = 'd M Y H:i';

    /**
     * 
     * @param type $date
     * @return DateTime object
     */
    public static function dateFromSQLDate($date) {
        return DateTime::createFromFormat(self::$DATE_FORMAT_SQL, $date);
    }

    /**
     * 
     * @param type $date
     * @return DateTime object
     */
    public static function dateFromSQLDateTime($date) {
        return DateTime::createFromFormat(self::$DATETIME_FORMAT_SQL, $date);
    }

    /**     * 
     * @param type $date
     * @return String date
     */
    public static function dateStringFromSQLDateTime($date) {
        $date = DateTime::createFromFormat(self::$DATETIME_FORMAT_SQL, $date);
        return $date->format(self::$DATE_FORMAT);
    }
    
      /**     * 
     * @param type $date
     * @return String date
     */
    public static function dateTimeStringFromSQLDateTime($date) {
        $date = DateTime::createFromFormat(self::$DATETIME_FORMAT_SQL, $date);
        return $date->format(self::$DATETIME_FORMAT);
    }

    /**     * 
     * @param type $date
     * @return String date
     */
    public static function dateStringFromSQLDate($date) {
        $date = DateTime::createFromFormat(self::$DATE_FORMAT_SQL, $date);
        return $date->format(self::$DATE_FORMAT);
    }

    /**     * 
     * @param type $date
     * @return String date
     */
    public static function dateStringFromDateTimeObject($date) {
        if ($date == null) {
            return "";
        }
        return $date->format(self::$DATE_FORMAT);
    }

    /**     * 
     * @param type $date
     * @return String date time 
     */
    public static function dateTimeStringFromSQL($date) {
        if ($date == null) {
            return "";
        }
        $date = DateTime::createFromFormat(self::$DATETIME_FORMAT_SQL, $date);
        return $date->format(self::$DATETIME_FORMAT);
    }

    public static function dateHrsMinStringFromSQL($date) {
        $date = DateTime::createFromFormat(self::$DATETIME_FORMAT_SQL, $date);
        return $date->format(self::$DATETIME_FORMAT_HRS_MIN);
    }

    public static function dateToSQLDate($param_date) {
        $date = DateTime::createFromFormat(self::$DATE_FORMAT, $param_date);
        return $date->format(self::$DATE_FORMAT_SQL);
    }

    /**
     * formats a DateTime object to a mysql date format
     * @param type $dateTime
     * @return type
     */
    public static function dateTimeToSQLDate($dateTime) {
        return $dateTime->format(self::$DATE_FORMAT_SQL);
    }

    /**
     * formats a DateTime object to a mysql dateTime format
     * @param type $dateTime
     * @return type
     */
    public static function dateTimeToSQLDateTime($dateTime) {
        return $dateTime->format(self::$DATETIME_FORMAT_SQL);
    }

}

class String {

    /**
     * @return bool Wether or not the given string $str starts with the givens tring $start.
     */
    public static function strStartsWith($str, $start) {
        return substr($str, 0, strlen($start)) == $start;
    }

    /**
     * @return bool Wether or not the given string $str ends with the givens tring $start.
     */
    public static function strEndsWith($str, $end) {
        return substr($str, -strlen($end), strlen($end)) == $end;
    }

    /**
     * Returns true if the first string starts with the second string 
     * strleft("Hello World", "Hello") == true
     * @param type $s1
     * @param type $s2
     * @return type
     */
    public static function strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

    /**
     * checks if a string is not empty
     * @param type $string
     * @return boolean
     */
    public static function isNotEmpty($string) {
        if (strlen($string) > 0) {
            return true;
        }
    }

    /* checks if a string is empty */
    public static function isEmpty($string) {
        if (strlen(strval($string)) == 0){
            return true;
        }else {
            return false;
        }
    }

}
