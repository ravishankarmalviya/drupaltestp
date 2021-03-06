<?php

namespace Drupal\bulk_message_system\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Action for test purposes only.
 *
 * @Action(
 *   id = "views_bulk_operations_bulk_message_system_action",
 *   label = @Translation("Bulk Message System"),
 *   type = "node",
 *   confirm = false,
 * )
 */
class BulkMessageSystemAction extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface, PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /*
     * All config resides in $this->configuration.
     * Passed view rows will be available in $this->context.
     * Data about the view used to select results and optionally
     * the batch context are available in $this->context or externally
     * through the public getContext() method.
     * The entire ViewExecutable object  with selected result
     * rows is available in $this->view or externally through
     * the public getView() method.
     */
    
    // Do some processing..
    // ...
    $node = \Drupal\node\Entity\Node::load($entity->id());
    $node->field_students_enrolled[] = ['target_id' => \Drupal::currentUser()->id()];
    $node->save();
    //drupal_set_message($entity->id());    
    return sprintf('User Enroll (forevents: %s)', print_r($entity->id(), TRUE));
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    $form['bulk_message_system_preconfig_setting'] = [
      '#title' => $this->t('BulkMessageSystem setting'),
      '#type' => 'textfield',
      '#default_value' => isset($values['bulk_message_system_preconfig_setting']) ? $values['bulk_message_system_preconfig_setting'] : '',
    ];
    return $form;
  }

  /**
   * Configuration form builder.
   *
   * If this method has implementation, the action is
   * considered to be configurable.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   The configuration form.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    
    return $form;
  }

  /**
   * Submit handler for the action configuration form.
   *
   * If not implemented, the cleaned form values will be
   * passed direclty to the action $configuration parameter.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // This is not required here, when this method is not defined,
    // form values are assigned to the action configuration by default.
    // This function is a must only when user input processing is needed.
    $this->configuration['bulk_message_system_title'] = $form_state->getValue('bulk_message_system_title');

    //print "<pre>";print_r($this->context);die;
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node' || $object->getEntityType() === 'user') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

}
