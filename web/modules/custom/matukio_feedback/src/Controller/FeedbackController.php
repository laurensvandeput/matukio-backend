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
      $response = $this->feedback_check_required_fields($response, $params);

      if (!empty($response)) {
        return new JsonResponse($response);
      }

      $new_feedback = $this->feedback_create_feedback($params);

      $node->get('field_event_feedback')
        ->appendItem(array('target_id' => $new_feedback->id()));
      $node->save();

      $response['response'] = '1';
      $response['response_text'] = $this->t("Feedback added for @asset", [
        '@asset' => $node->getTitle()
      ]);
    }
    else {
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
      $response = $this->feedback_check_required_fields($response, $params);

      if (!empty($response)) {
        return new JsonResponse($response);
      }

      $new_feedback = $this->feedback_create_feedback($params);

      $node->get('field_asset_feedback')
        ->appendItem(array('target_id' => $new_feedback->id()));
      $node->save();

      $response['response'] = '1';
      $response['response_text'] = $this->t("Feedback added for @asset", [
        '@asset' => $node->getTitle()
      ]);
    }
    else {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Could not find asset with id @id", [
        '@id' => $id
      ]);
    }
    return new JsonResponse($response);
  }

  private function feedback_check_required_fields($response, $params) {
    if (empty($params['feedback'])) {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Field feedback required");
    }
    if (empty($params['user'])) {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Field user required");
    }
    if (!isset($params['rating'])) {
      $response['response'] = '0';
      $response['response_text'] = $this->t("Field rating required");
    }
    else {
      if (!is_numeric($params['rating'])) {
        $response['response'] = '0';
        $response['response_text'] = $this->t("Rating should be a number");
      }
    }
    return $response;
  }

  /**
   * @param $params
   * @return \Drupal\Core\Entity\EntityInterface
   */
  public function feedback_create_feedback($params) {
    $new_feedback_values = array(
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

    $new_feedback = entity_create('node', $new_feedback_values);
    $new_feedback->save();
    return $new_feedback;
  }
}
