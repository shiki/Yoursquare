<?php

namespace Yoursquare;

/**
 * Extends the FoursquareApi to allow adding custom methods without modifying the parent class
 */
class FoursquareClient extends \FoursquareApi
{
  /**
   * Getter function for the protected property FoursquareApi::$RedirectUri
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->RedirectUri;
  }
}
