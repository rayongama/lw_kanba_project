<?php

namespace Kanban;

use Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

class Configurator
{

  private static $loaded = false;

  private static $VARS = [
    "TITLE" => "",

    "DB_URL" => "",
    "DB_NAME" => "",
    "DB_USERNAME" => "",
    "DB_PASSWORD" => "",
  ];

  private static function load()
  {
    if (!Configurator::$loaded) {
      $raw = parse_ini_file(Loader::resolvePath("config.ini"));
      foreach ($raw as $k => $v) {
        Configurator::$VARS[$k] = $v;
      }
      Configurator::$loaded = true;
    }
  }

  private static function save()
  {
    if (Configurator::$loaded) {
      Configurator::write_ini_file(Loader::resolvePath("config.ini"), Configurator::$VARS);
      Configurator::$loaded = false;
    }
  }

  public static function getEntry($key)
  {
    $key = strtoupper($key);
    if (!Configurator::$loaded) {
      Configurator::load();
    }

    if (key_exists($key, Configurator::$VARS)) {
      return Configurator::$VARS[$key];
    }
    return null;
  }

  public static function setEntry($key, $value)
  {
    $key = strtoupper($key);
    if (!Configurator::$loaded) {
      Configurator::load();
    }

    Configurator::$VARS[$key] = $value;
    Configurator::save();
  }

  /**
   * Write an ini configuration file
   * @see https://stackoverflow.com/questions/5695145/how-to-read-and-write-to-an-ini-file-with-php
   *
   * @param string $file
   * @param array $array
   * @return bool
   */
  private static function write_ini_file($file, $array = [])
  {
    // check first argument is string
    if (!is_string($file)) {
      throw new \InvalidArgumentException('Function argument 1 must be a string.');
    }

    // check second argument is array
    if (!is_array($array)) {
      throw new \InvalidArgumentException('Function argument 2 must be an array.');
    }

    // process array
    $data = array();
    foreach ($array as $key => $val) {
      if (is_array($val)) {
        $data[] = "[$key]";
        foreach ($val as $skey => $sval) {
          if (is_array($sval)) {
            foreach ($sval as $_skey => $_sval) {
              if (is_numeric($_skey)) {
                $data[] = $skey . '[] = ' . (is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"' . $_sval . '"'));
              } else {
                $data[] = $skey . '[' . $_skey . '] = ' . (is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"' . $_sval . '"'));
              }
            }
          } else {
            $data[] = $skey . ' = ' . (is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"' . $sval . '"'));
          }
        }
      } else {
        $data[] = $key . ' = ' . (is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"' . $val . '"'));
      }
      // empty line
      $data[] = null;
    }

    // open file pointer, init flock options
    $fp = fopen($file, 'w');
    $retries = 0;
    $max_retries = 100;

    if (!$fp) {
      return false;
    }

    // loop until get lock, or reach max retries
    do {
      if ($retries > 0) {
        usleep(rand(1, 5000));
      }
      $retries += 1;
    } while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

    // couldn't get the lock
    if ($retries == $max_retries) {
      return false;
    }

    // got lock, write data
    fwrite($fp, implode(PHP_EOL, $data) . PHP_EOL);

    // release lock
    flock($fp, LOCK_UN);
    fclose($fp);

    return true;
  }

}