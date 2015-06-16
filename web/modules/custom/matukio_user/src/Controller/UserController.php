<?php

/**
 * @file
 * Contains Drupal\matukio_user\Controller\UserController.
 */

namespace Drupal\matukio_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UserController.
 *
 * @package Drupal\matukio_user\Controller
 */
class UserController extends ControllerBase {
  /**
   * Register a user
   * @param $id : the id of the event
   * @param $name : the name to register
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function register($id, $name) {
    $node = Node::load($id);
    $params = array();
    if ($node && $node->getType() == 'event') {
      $this->matukio_remove_unused_values_from_field($node, 'field_event_members');
      $values = $node->get('field_event_members')->getValue();

      if (!$this->matukio_user_is_registered($name, $values)) {
        $node->get('field_event_members')->appendItem($name);
        $node->save();
        $params['response_text'] = $this->t('Hello @name, welcome to @event!', [
          '@name' => $name,
          '@event' => $node->getTitle(),
        ]);
        $params['response'] = '1';
      }
      else {
        $params['response_text'] = $this->t('@name already is registered for @event!', [
          '@name' => $name,
          '@event' => $node->getTitle(),
        ]);
        $params['response'] = '2';
      }
    }
    else {
      $params['response'] = '0';
      $params['response_text'] = $this->t("Error: couldn't find event with id @id", [
        '@id' => $id
      ]);
    }
    return new JsonResponse($params);
  }

  /**
   * Unregister a user
   * @param $id : the event id
   * @param $name : the name of the user
   * @return array
   */
  public function unregister($id, $name) {
    $node = Node::load($id);
    $params = array();
    if ($node && $node->getType() == 'event') {
      $this->matukio_remove_unused_values_from_field($node, 'field_event_members');

      if ($this->matukio_user_is_registered($name, $values)) {
        $values = $node->get('field_event_members')->getValue();
        foreach ($values as $key => $value) {
          if ($value['value'] == $name) {
            $index = $key;
          }
        }

        $node->get('field_event_members')->removeItem($index);
        $node->updateOriginalValues();
        $node->save();
        $params['response_text'] = $this->t('@name is unregistered from @event!', [
          '@name' => $name,
          '@event' => $node->getTitle(),
        ]);
        $params['response'] = '1';
      }
      else {
        $params['response_text'] = $this->t('@name was not registered for @event!', [
          '@name' => $name,
          '@event' => $node->getTitle(),
        ]);
        $params['response'] = '2';
      }
    }
    else {
      $params['response'] = '0';
      $params['response_text'] = $this->t("Error: couldn't find event with id @id", [
        '@id' => $id
      ]);
    }
    return new JsonResponse($params);
  }

  /**
   * Check if a user is registered in the values from a field
   * @param $name
   * @param $values
   * @return bool
   */
  protected function matukio_user_is_registered($name, $values) {
    foreach ($values as $value) {
      if ($value['value'] == $name) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Clear empty lines from multi value field. Use at own risk.
   * @param \Drupal\node\Entity\Node $node
   * @param $field
   */
  public function matukio_remove_unused_values_from_field(Node &$node, $field) {
    $values = $node->get($field)->getValue();
    $filtered = array();
    foreach ($values as $value) {
      if (!empty($value['value'])) {
        $filtered[] = $value['value'];
      }
    }

    if (count($filtered) !== count($values)) {
      for ($a = 0; $a < count($values); $a++) {
        $node->get($field)->removeItem(0);
      }
      foreach ($filtered as $item) {
        $node->get($field)->appendItem($item);
      }
      $node->save();
    }
  }
}
