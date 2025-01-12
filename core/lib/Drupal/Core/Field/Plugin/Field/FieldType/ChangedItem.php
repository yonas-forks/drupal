<?php

namespace Drupal\Core\Field\Plugin\Field\FieldType;

use Drupal\Core\Entity\SynchronizableInterface;
use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\ChangedFieldItemList;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the 'changed' entity field type.
 *
 * Based on a field of this type, entity types can easily implement the
 * EntityChangedInterface.
 *
 * @see \Drupal\Core\Entity\EntityChangedInterface
 */
#[FieldType(
  id: "changed",
  label: new TranslatableMarkup("Last changed"),
  description: new TranslatableMarkup("An entity field containing a UNIX timestamp of when the entity has been last updated."),
  default_widget: "datetime_timestamp",
  default_formatter: "timestamp",
  no_ui: TRUE,
  list_class: ChangedFieldItemList::class,
)]
class ChangedItem extends CreatedItem {

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    parent::preSave();

    // Set the timestamp to request time if it is not set.
    if (!$this->value) {
      $this->value = \Drupal::time()->getRequestTime();
    }
    else {
      // On an existing entity translation, the changed timestamp will only be
      // set to the request time automatically if at least one other field value
      // of the entity has changed. This detection does not run on new entities
      // and will be turned off if the changed timestamp is set manually before
      // save, for example during migrations or by using
      // \Drupal\content_translation\ContentTranslationMetadataWrapperInterface::setChangedTime().
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $this->getEntity();
      if (!$entity instanceof SynchronizableInterface || !$entity->isSyncing()) {
        $original = $entity->getOriginal();
        $langcode = $entity->language()->getId();
        if (!$entity->isNew() && $original && $original->hasTranslation($langcode)) {
          $original_value = $original->getTranslation($langcode)->get($this->getFieldDefinition()->getName())->value;
          if ($this->value == $original_value && $entity->hasTranslationChanges()) {
            $this->value = \Drupal::time()->getRequestTime();
          }
        }
      }
    }
  }

}
