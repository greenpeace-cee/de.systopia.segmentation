<?php

namespace Civi\Api4\Service\Spec\Provider;

use Civi\Api4\Query\Api4SelectQuery;
use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;

/**
 * @service
 * @internal
 */
class ActivityContactSpecProvider implements Generic\SpecProviderInterface {

  /**
   * @inheritDoc
   */
  public function modifySpec(RequestSpec $spec) {
    $segmentation_id_field = new FieldSpec('segmentation_index_id', 'ActivityContact', 'Integer');

    $segmentation_id_field->setTitle(ts('Segmentation ID'))
      ->setLabel(ts('Segmentation ID'))
      ->setDescription(ts('ID of the SegmentationIndex this ActivityContact is linked to'))
      ->setColumnName('id')
      ->setFkEntity('SegmentationIndex')
      ->setInputType('EntityRef')
      ->setSqlRenderer([__CLASS__, 'renderSqlForActivityContactSegmentationID']);

    $spec->addFieldSpec($segmentation_id_field);

    $segmentation_name_field = new FieldSpec('segmentation_index_name', 'ActivityContact', 'String');

    $segmentation_name_field->setTitle(ts('Segmentation Name'))
      ->setLabel(ts('Segmentation Name'))
      ->setDescription(ts('Name of the SegmentationIndex this ActivityContact is linked to'))
      ->setColumnName('id')
      ->setInputType('Text')
      ->setSqlRenderer([__CLASS__, 'renderSqlForActivityContactSegmentationName']);

    $spec->addFieldSpec($segmentation_name_field);
  }

  /**
   * @inheritDoc
   */
  public function applies($entity, $action) {
    return $entity === 'ActivityContact' && $action === 'get';
  }

  public static function renderSqlForActivityContactSegmentationID(array $field, Api4SelectQuery $query): string {
    return "(
      SELECT si.id
      FROM civicrm_segmentation_index si
      INNER JOIN civicrm_activity_contact_segmentation acs
        ON acs.activity_contact_id = {$field['sql_name']}
        AND si.id = acs.segment_id
    )";
  }

  public static function renderSqlForActivityContactSegmentationName(array $field, Api4SelectQuery $query): string {
    return "(
      SELECT si.name
      FROM civicrm_segmentation_index si
      INNER JOIN civicrm_activity_contact_segmentation acs
        ON acs.activity_contact_id = {$field['sql_name']}
        AND si.id = acs.segment_id
    )";
  }

}
