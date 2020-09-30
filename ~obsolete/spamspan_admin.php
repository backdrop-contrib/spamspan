<?php

/**
 * @file
 * This module implements the spamspan technique (http://www.spamspan.com ) for hiding email addresses from spambots.
 *
 * Move less frequently used code out of the .module file.
 */

// TODO: refactor code to drop spamspan_admin class
class spamspan_admin {
  protected $defaults;
  protected $display_name = 'SpamSpan';
  protected $filter;
  protected $help = '<p>The SpamSpan module obfuscates email addresses to help prevent spambots from collecting them. Read the <a href="@url">SpamSpan configuration page</a>.</p>';
  protected $page;

  function __construct() {
    $info = spamspan_filter_info();
    $this->defaults = $info['spamspan']['default settings'];
  }
  function defaults() {
    return $this->defaults;
  }
  function display_name() {
    return $this->display_name;
  }
  function filter_is() {
    return isset($this->filter);
  }
  function filter_set($filter) {
    $this->filter = $filter;
  }

  /**
   * @function
   * Generic logging function. Used mainly for development.
   */
  function log($message, $variables = array()) {
    watchdog($this->display_name, $message, $variables);
  }

  /**
   * A helper function for the callbacks
   *
   * Replace an email addresses which has been found with the appropriate
   * <span> tags
   *
   * @param $name
   *  The user name
   * @param $domain
   *  The email domain
   * @param $contents
   *  The contents of any <a> tag
   * @param $headers
   *  The email headers extracted from a mailto: URL
   * @param $vars
   *  Optional parameters to be implemented later.
   * @param $settings
   *  Provide specific settings. They replace anything set through filter_set().
   * @return
   *  The span with which to replace the email address
   */
  function output($name, $domain, $contents = '', $headers = array(), $vars = array(), $settings = NULL) {
    if ($settings === NULL) {
      $settings = $this->defaults;
      if ($this->filter_is()) {
        $settings = $this->filter->settings;
      }
    }

    // processing for forms
    if (!empty($settings['spamspan_use_form'])) {
      $email = urlencode(base64_encode($name . '@' . $domain));

      //put in the defaults if nothing set
      if (empty($vars['custom_form_url'])) {
        $vars['custom_form_url'] = $settings['spamspan_form_default_url'];
      }
      if (empty($vars['custom_displaytext'])) {
        $vars['custom_displaytext'] = t($settings['spamspan_form_default_displaytext']);
      }
      $vars['custom_form_url'] = strip_tags($vars['custom_form_url']);
      $vars['custom_displaytext'] = strip_tags($vars['custom_displaytext']);

      $url_parts = parse_url($vars['custom_form_url']);
      if (!$url_parts) {
        $vars['custom_form_url'] = '';
      }
      else if (empty($url_parts['host'])) {
        $vars['custom_form_url'] = base_path() . trim($vars['custom_form_url'], '/');
      }

      $replace = array('%url' => $vars['custom_form_url'], '%displaytext' => $vars['custom_displaytext'], '%email' => $email);

      $output = strtr($settings['spamspan_form_pattern'], $replace);
      return $output;
    }

    $at = $settings['spamspan_at'];
    if ($settings['spamspan_use_graphic']) {
      $at = theme('spamspan_at_sign', array('settings' => $settings));
    }

    if ($settings['spamspan_dot_enable']) {
      // Replace .'s in the address with [dot]
      $name = str_replace('.', '<span class="o">' . $settings['spamspan_dot'] . '</span>', $name);
      $domain = str_replace('.', '<span class="o">' . $settings['spamspan_dot'] . '</span>', $domain);
    }
    $output = '<span class="u">' . $name . '</span>' . $at . '<span class="d">' . $domain . '</span>';


    // if there are headers, include them as eg (subject: xxx, cc: zzz)
    // we replace the = in the headers by ": " to look nicer
    if (count($headers)) {
      foreach ($headers as $key => $header) {
        // check if header is already urlencoded, if not, encode it
        if ($header == rawurldecode($header)) {
          $header = rawurlencode($header);
          //replace the first = sign
          $header = preg_replace('/%3D/', ': ', $header, 1);
        }
        else {
          $header = str_replace('=', ': ', $header);
        }

        $headers[$key] = $header;
      }
      $output .= '<span class="h"> (' . check_plain(implode(', ', $headers)) . ') </span>';
    }

    // If there are tag contents, include them, between round brackets.
    // Remove emails from the tag contents, otherwise the tag contents are themselves
    // converted into a spamspan, with undesirable consequences - see bug #305464.
    if (!empty($contents)) {
      $contents = preg_replace('!' . SPAMSPAN_EMAIL . '!ix', '', $contents);

      // remove anything except certain inline elements, just in case.  NB nested
      // <a> elements are illegal.
      $contents = filter_xss($contents, array('em', 'strong', 'cite', 'b', 'i', 'code', 'span', 'img', 'br'));

      if (!empty($contents)) {
        $output .= '<span class="t"> (' . $contents . ')</span>';
      }
    }

    // put in the extra <a> attributes
    // this has to come after the xss filter, since we want comment tags preserved
    if (!empty($vars['extra_attributes'])) {
      $output .= '<span class="e"><!--'. strip_tags($vars['extra_attributes']) .'--></span>';
    }

    $output = '<span class="spamspan">' . $output . '</span>';

    return $output;
  }


  /**
   * Return the admin page.
   * External text should be checked: = array('#markup' => check_plain($format->name));
   */
  function page_object() {
    if (!isset($this->page)) {
      $this->page = new spamspan_admin_page($this);
    }
    return $this->page;
  }
  function page($form, &$form_state) {
    return $this->page_object()->form($form, $form_state);
  }
  function page_submit($form, &$form_state) {
    $this->page_object()->submit($form, $form_state);
  }
}

