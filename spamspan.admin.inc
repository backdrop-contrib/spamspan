<?php
/**
 * @file
 * Administration page for the SpamSpan module.
 */


/**
 * Return the settings form to experiment with SpamSpan.
 */
function spamspan_settings_form($form, &$form_state) {

  $form[] = array(
    '#markup' => t('The SpamSpan module obfuscates email addresses to help prevent spambots from collecting them. It will produce clickable links if JavaScript is enabled, and will show the email address as <code>example [at] example [dot] com</code> if the browser does not support JavaScript.'),
    '#prefix' => '<p>',
    '#suffix' => '</p>',
  );

  $form['configure'] = array(
    '#type' => 'fieldset',
    '#title' => t('Configuration'),
  );
  $form['configure'][] = array(
    '#markup' => t('
<ol>
  <li>Read the list of text formats at <a href="/admin/config/content/formats">Text formats</a>.</li>
  <li>Select <strong>configure</strong> for any format requiring email addresses.</li>
  <li>In <strong>Enable filters</strong>, select <em>SpamSpan email address encoding filter</em>.</li>
  <li>In <strong>Filter processing order </strong>, move SpamSpan below <em>Convert line breaks into HTML</em> and above <em>Convert URLs into links</em>.</li>
  <li>If you use the <strong>Limit allowed HTML tags</strong> filter, make sure that &lt;span&gt; is one of the allowed tags.</li>
  <li>Select <strong>SpamSpan email address encoding filter</strong> to configure SpamSpan for the text format.</li>
  <li>Select <strong>Save configuration</strong> to save your changes.</li>
</ol>'),
    '#prefix' => '<p>',
    '#suffix' => '</p>',
  );

  $filter_info = spamspan_filter_info();
  $defaults = $filter_info['spamspan']['default settings'];
  $form_state['defaults'] = $defaults;

  $form['defaults_list'] = array(
    '#type' => 'fieldset',
    '#title' => t('Defaults'),
  );
  $defaults_list = array();
  foreach ($defaults as $name => $value) {
    if ($value === TRUE) {
      $value = 'TRUE';
    }
    elseif ($value === FALSE) {
      $value = 'FALSE';
    }
    $defaults_list[] = $name . ': <strong>' . htmlentities($value) . '</strong>';
  }
  $form['defaults_list'][] = array(
  '#markup' => t('
These defaults are used for new filters and for <code>spamspan()</code> when there is no filter specified.
<ul>
<li>' . implode('</li><li>', $defaults_list) . '</li>
</ul>'),
    '#prefix' => '<p>',
    '#suffix' => '</p>',
  );

  $form['test'] = array(
    '#type' => 'fieldset',
    '#title' => t('Test'),
  );
  $form['test'][] = array(
    '#markup' => t('Test the <code>spamspan()</code> function using the following <strong>Test text</strong> field. Enter text containing an email address, then hit the Test button.'),
    '#prefix' => '<p>',
    '#suffix' => '</p>',
  );

  $test_text = config_get('spamspan.settings', 'test_text');
  $form['test']['test_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Test Text'),
    '#size' => 80,
    '#maxlength' => 200,
    '#default_value' => $test_text,
  );

  if (isset($form_state['test_defaults'])) {
    $test_defaults = $form_state['test_defaults'];
  }
  else {
    $test_defaults = $defaults;
  }
  $filter = (object) array('settings' => $test_defaults);
  $filter_settings_form = _spamspan_filter_settings(array(), array(), $filter, '');
  $form['test']['filter_settings'] = $filter_settings_form;

  $form['test']['results'] = array(
    '#type' => 'fieldset',
    '#title' => t('Results'),
  );
  $test_result = spamspan($test_text, $test_defaults);
  $form['test']['results']['test_js'] = array(
    '#markup' => '<p>The result passed through <code>spamspan()</code> and processed by Javascript:</p><div style="background-color: #ccffcc;">' .
      $test_result . '</div>');
  $form['test']['results']['test_result'] = array(
    '#markup' => '<p>The result passed through <code>spamspan()</code> but not processed by Javascript:</p><div style="background-color: #ccccff;">' .
      str_replace('class="spamspan"', '', $test_result) . '</div>');
  $form['test']['results']['test_as_html'] = array(
    '#markup' => '<p>The HTML in the result:</p><div style="background-color: #ffcccc;">' .
      nl2br(htmlentities($test_result)) . '</div>');

  $form['test']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Test'),
  );

  return $form;
}


/**
 * Submission function for admin settings form.
 */
function spamspan_settings_form_submit($form, &$form_state) {

  // Store the test text in configuration so that it persists across page loads.
  $test_text = check_plain($form_state['values']['test_text']);
  config_set('spamspan.settings', 'test_text', $test_text);

  // Stash the submitted settings values in $form_state['test_defaults'] so that
  // we can use it for testing over and over but will reset the next time we
  // load the admin page.
  $test_defaults = $form_state['defaults'];
  foreach ($test_defaults as $field => $value) {
    if (isset($form_state['values'][$field])) {
      $test_defaults[$field] = $form_state['values'][$field];
    }
  }
  $form_state['test_defaults'] = $test_defaults;

  // Force the form to rebuild to use the current settings values to compute
  // the displayed results.
  $form_state['rebuild'] = TRUE;
}
