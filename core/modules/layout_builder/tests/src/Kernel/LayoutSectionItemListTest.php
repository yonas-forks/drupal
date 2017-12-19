<?php

namespace Drupal\Tests\layout_builder\Kernel;

use Drupal\entity_test\Entity\EntityTestBaseFieldDisplay;

/**
 * Tests the field type for Layout Sections.
 *
 * @coversDefaultClass \Drupal\layout_builder\Field\LayoutSectionItemList
 *
 * @group layout_builder
 */
class LayoutSectionItemListTest extends SectionStorageTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'field',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function getEntity(array $section_data) {
    $this->installEntitySchema('entity_test_base_field_display');
    layout_builder_add_layout_section_field('entity_test_base_field_display', 'entity_test_base_field_display');

    $entity = EntityTestBaseFieldDisplay::create([
      'name' => 'The test entity',
      'layout_builder__layout' => $section_data,
    ]);
    $entity->save();
    return $entity->get('layout_builder__layout');
  }

}
