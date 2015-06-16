<?php

/**
 * @file
 * Contains Drupal\matukio_feedback\Controller\FeedbackController.
 */

namespace Drupal\matukio_feedback\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FeedbackController.
 *
 * @package Drupal\matukio_feedback\Controller
 */
class FeedbackController extends ControllerBase {
  /**
   * Add feedback to an event
   * @param $id : the id of the event
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function addFeedbackToEvent($id, RouteMatchInterface $route_match, Request $request) {
    $node = Node::load($id);
    $response = array();
    if ($node && $node->getType() == 'event') {

      $params = $request->request->all();

      $new_page_values = array(
        'type' => 'feedback',
        'title' => 'feedback_' . time(),
        'field_feedback_feedback' => array(
          'value' => $params['feedback'],
          'format' => 'basic_html',
        ),
        'field_feedback_user' => array(
          'value' => $params['user'],
        ),
        'field_feedback_rating' => array(
          'value' => $params['rating'],
        ),
      );

      $new_page = entity_create('node', $new_page_values);
      $new_page->save();

      $node->get('field_event_feedback')
        ->appendItem(array('target_id' => $new_page->id()));
      $node->save();

      $response['response'] = '1';
      $response['response_text'] = $this->t("Feedback added for @asset", [
        '@asset' => $node->getTitle()
      ]);
    } else {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Could not find event with id @id", [
        '@id' => $id
      ]);
    }
    return new JsonResponse($response);
  }

  /**
   * Add feedback to an asset
   * @param $id : the id of the asset
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function addFeedbackToAsset($id, RouteMatchInterface $route_match, Request $request) {
    $node = Node::load($id);
    $response = array();
    if ($node && $node->getType() == 'asset') {

      $params = $request->request->all();

      $new_page_values = array(
        'type' => 'feedback',
        'title' => 'feedback_' . time(),
        'field_feedback_feedback' => array(
          'value' => $params['feedback'],
          'format' => 'basic_html',
        ),
        'field_feedback_user' => array(
          'value' => $params['user'],
        ),
        'field_feedback_rating' => array(
          'value' => $params['rating'],
        ),
      );

      $new_page = entity_create('node', $new_page_values);
      $new_page->save();

      $node->get('field_asset_feedback')
        ->appendItem(array('target_id' => $new_page->id()));
      $node->save();

      $response['response'] = '1';
      $response['response_text'] = $this->t("Feedback added for @asset", [
        '@asset' => $node->getTitle()
      ]);
    } else {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Could not find asset with id @id", [
        '@id' => $id
      ]);
    }
    return new JsonResponse($response);
  }
}
