<?php

namespace Ardyn;

use Symfony\Component\HttpFoundation\Response;

/**
 * Export a multidimensional array as a Excel CSV file
 */
class CsvExport {

 /**
  * Properties
  */
  protected $buffer = '';



 /**
  * Constants
  */
  const DELIMITER = ',';
  const ENCLOSURE = '"';
  const EOL = PHP_EOL;



 /**
  * Create a CSV file from the given array and return data
  *
  * @access public
  * @param array $array
  * @return string
  */
  public function csv($array) {

    // Valid array
    if ( ! is_array($array) || ! is_array(current($array)) )
      $array = array(array('No Data.'));

    $this->header(current($array));
    $this->body($array);

  } /* function csv */



 /**
  * Create the column headers from the array keys
  *
  * @access private
  * @param array $headers
  * @return void
  */
  private function header($headers) {

    $this->buffer = $this->line(array_keys($headers));

  } /* function header */



 /**
  * Convert the multidimensional array to a string
  *
  * @access private
  * @param array $array
  * @return void
  */
  private function body($array) {

    foreach ($array as $line)
      $this->buffer .= $this->line($line);

  } /* function header */



 /**
  * Convert an array to a CSV line
  *
  * @access private
  * @param array $line
  * @return string
  */
  private function line($array) {

    if ( ! is_array($array) )
      return;

    $str = '';

    foreach ($array as $cell) {

      // Clean the data
      // $cell = standardize_new_lines($cell);
      // $cell = remove_invisible_characters($cell);

      // Enclose enclosure character
      $cell = str_replace(self::ENCLOSURE, self::ENCLOSURE . self::ENCLOSURE, $cell);

      // Enclose cell contents if necessary
      if ( (strpos($cell, self::DELIMITER) !== false) ||
           (strpos($cell, self::ENCLOSURE) !== false) ||
           (strpos($cell, self::EOL) !== false) )
        $cell = self::ENCLOSURE . $cell . self::ENCLOSURE;

      // May have to handle escape characters at some point


      $str .= $cell . self::DELIMITER;

    }

    return rtrim($str, self::DELIMITER) . self::EOL;

  } /* function line */



 /**
  * Ouput the contents
  *
  * @access private
  * @param void
  * @return \Illuminate\Http\Response
  */
  public function respond() {

    $headers = [
      'Content-Type' => 'application/csv',
      'Content-Disposition', "attachment;Filename={$this->getFilename()}",
    ];
   
    return Response::create($this->buffer, 200, $headers);

  } /* function respond */



  /**
   * Create a filename
   *
   * @access protected
   * @param void
   * @return string
   */
  protected function getFilename() {

    return substr(md5(time()),0, 12) . '.csv';

  } /* function getFilename */

} /* class Export */

/* EOF */
