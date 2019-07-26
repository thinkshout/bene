<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Define application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements Context, SnippetAcceptingContext {
  const DEFAULT_PASSWORD = "admin";

  /**
   * Asserts that a user is logged in.
   *
   * @Given I am logged in as user with email :email
   */
  public function assertLoggedInAsUser($email) {
    $this->getSession()->visit($this->locatePath('/user'));

    $page_element = $this->getSession()->getPage();
    $page_element->fillField($this->getDrupalText('username_field'), $email);
    $page_element->fillField($this->getDrupalText('password_field'), self::DEFAULT_PASSWORD);
    $submit = $page_element->findButton($this->getDrupalText('log_in'));
    if (!$submit) {
      throw new \Exception('No submit button on "' . $this->getSession()->getCurrentUrl() . '".');
    }
    $submit->click();

    if ($this->loggedIn()) {
      return;
    }

    throw new \Exception('Not logged in.');
  }

  /**
   * Checks that access was denied for a page.
   *
   * @Then I should get an access denied error
   */
  public function assertAccessDenied() {
    $status_code = $this->getSession()->getStatusCode();
    if ($status_code != 403) {
      // Look for the error message div.
      $errorNode = $this->getSession()
        ->getPage()
        ->find('css', '.messages--error');
      if ($errorNode) {
        if (strpos($errorNode->getText(), 'Access denied.') === FALSE) {
          throw new Exception("No access denied message displayed.");
        }
      }
      else {
        throw new Exception("No error message displayed.");
      }
    }
  }

  /**
   * Waits for a progress bar to finish.
   *
   * @Given I wait for the progress bar to finish
   */
  public function iWaitForTheProgressBarToFinish() {
    $this->iFollowMetaRefresh();
  }

  /**
   * Follows a meta refresh tag.
   *
   * @Given I follow meta refresh
   *
   * https://www.drupal.org/node/2011390
   */
  public function iFollowMetaRefresh() {
    while ($refresh = $this->getSession()->getPage()->find('css', 'meta[http-equiv="Refresh"]')) {
      $content = $refresh->getAttribute('content');
      $url = str_replace('0; URL=', '', $content);
      $this->getSession()->visit($url);
    }
  }

  /**
   * Waits a certain amount of seconds.
   *
   * @Given I wait :seconds seconds
   */
  public function iWaitSeconds($seconds) {
    sleep($seconds);
  }

  /**
   * Waits for an element to appear.
   *
   * @Given I wait :seconds seconds or until I see :text
   */
  public function iWaitSecondsOrUntiliSee($seconds, $text) {
    $errorNode = $this->spin(function ($context) use ($text) {
      $node = $context->getSession()->getPage()->find('named', ['content', $text]);
      if (!$node) {
        return FALSE;
      }
      return $node->isVisible();
    }, $seconds);

    // Throw to signal a problem if we were passed back an error message.
    if (is_object($errorNode)) {
      throw new Exception("Error detected when waiting for '$text': " . $errorNode->getText());
    }
  }

  /**
   * Displays a spinner until a timer runs out or until a callback return TRUE.
   *
   * @param callable $lambda
   *   A callback.
   * @param int $wait
   *   How many seconds to wait. Defaults to 60.
   *
   * @return \Behat\Mink\Element\NodeElement|bool
   *   An error node if available, or TRUE if successful.
   *
   * @throws \Exception
   *   Throws an exception if the timeout is reached.
   */
  public function spin(callable $lambda, $wait = 60) {
    for ($i = 0; $i <= $wait; $i++) {
      if ($i > 0) {
        sleep(1);
      }

      $debugContent = $this->getSession()->getPage()->getContent();
      file_put_contents("/tmp/mink/debug-" . $i, "\n\n\n=================================\n$debugContent\n=================================\n\n\n");

      try {
        if ($lambda($this)) {
          return TRUE;
        }
      }
      catch (Exception $e) {
        // Do nothing.
      }

      // If we do not see the text we are waiting for, fail fast if
      // we see a Drupal 8 error message pane on the page.
      $node = $this->getSession()->getPage()->find('named', ['content', 'Error']);
      if ($node) {
        $errorNode = $this->getSession()->getPage()->find('css', '.messages--error');
        if ($errorNode) {
          return $errorNode;
        }
        $errorNode = $this->getSession()->getPage()->find('css', 'main');
        if ($errorNode) {
          return $errorNode;
        }
        return $node;
      }
    }

    $backtrace = debug_backtrace();

    throw new Exception(
      "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
      $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
    );
  }

}
