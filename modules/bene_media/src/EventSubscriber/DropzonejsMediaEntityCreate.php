<?php

namespace Drupal\bene_media\EventSubscriber;

use Drupal\dropzonejs\Events;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber MyEventSubscriber.
 */
class DropzonejsMediaEntityCreate implements EventSubscriberInterface {
  /**
   * Code that should be triggered on event specified
   */
  public function onRespond(DropzoneMediaEntityCreateEvent $event) {
    // The RESPONSE event occurs once a response was created for replying to a request.
    $response = $event->getResponse();

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[Events\Events::MEDIA_ENTITY_CREATE][] = ['onRespond'];
    return $events;
  }

}