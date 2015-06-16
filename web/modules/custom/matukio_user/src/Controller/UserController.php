<?php

/**
 * @file
 * Contains Drupal\matukio_user\Controller\UserController.
 */

namespace Drupal\matukio_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class UserController.
 *
 * @package Drupal\matukio_user\Controller
 */
class UserController extends ControllerBase {
  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function register($id, $name) {
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Hello @name to @event!', ['@name' => $name, '@event' => $id])
    ];
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function unregister($id, $name) {
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Hello @name!', ['@name' => $name])
    ];
  }

}
