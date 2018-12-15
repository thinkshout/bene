<?php

namespace Drupal\bene_promo_redirect\EventSubscriber;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect Event Subscriber.
 */
class RedirectPromo implements EventSubscriberInterface {

  /**
   * Triggered when system sends response.
   */
  public function modifyIntercept(GetResponseEvent $event) {
    $config = \Drupal::config('bene_promo_redirect.settings');
    if ($config->get('enabled')) {
      $destination = $config->get('dest');
      if (!empty($destination) && UrlHelper::isValid($destination)) {
        if (\Drupal::request()->get('promo_redirect') == 'no') {
          // Don't redirect when asked not to redirect:
          return;
        }
        $route = \Drupal::routeMatch()->getRouteObject();
        if (\Drupal::service('router.admin_context')->isAdminRoute($route)) {
          // Don't redirect admin pages.
          return;
        }
        $request = \Drupal::request();
        $current_uri = $request->getRequestUri();
        // Don't redirect User pages.
        if (strpos($current_uri, "/user") === 0) {
          return;
        }
        // Now let's look closer at our path specifications:
        if ($config->get('redirect_all')) {
          $pathmatch = TRUE;
        }
        else {
          $pathmatch = FALSE;
          foreach ($config->get('paths') as $path) {
            if (in_array($current_uri, [$path, "/$path"])) {
              $pathmatch = TRUE;
              continue;
            }
          }
        }
        if ($pathmatch) {
          // We have a path that we want to redirect. Now make sure we are
          // coming from somewhere outside the website and that we are not
          // coming back from the redirect destination, either:
          $headers = $request->server->getHeaders();
          if (isset($headers['REFERER'])) {
            $destination_parts = parse_url($config->get('dest'));
            $safe_referrers = [
              $headers['HOST'],
              $destination_parts['host'],
            ];
            $referrer_parts = parse_url($headers['REFERER']);
            if (in_array($referrer_parts['host'], $safe_referrers)) {
              // We are coming from inside the site. Don't redirect.
              return;
            }
          }
          \Drupal::service('page_cache_kill_switch')->trigger();
          // Set redirect response.
          $redir = new TrustedRedirectResponse($config->get('dest'), '302');
          $redir->headers->set('Cache-Control', 'public, max-age=0');
          $event->setResponse($redir);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Listen for response event from system and intercept.
    $events[KernelEvents::REQUEST][] = ['modifyIntercept'];
    return $events;
  }

}
