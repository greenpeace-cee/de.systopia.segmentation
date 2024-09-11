<?php

use CRM_Segmentation_ExtensionUtil as E;

class CRM_Segmentation_BAO_SegmentationOrder extends CRM_Segmentation_DAO_SegmentationOrder {

  /**
   * Create a new or update an existing SegmentationOrder
   *
   * @param array $params
   *
   * @return CRM_Segmentation_BAO_SegmentationOrder
   */
  public static function createOrUpdate($params) {
    if (empty($params['id'])) {
      // Create a new one
      $bao = new self();
      $params = array_intersect_key($params, self::fields());
    } else {
      // Update an existing one
      $bao = self::findById($params['id']);

      // Do not update references to Campaign or SegmentationIndex
      $params = array_intersect_key($params, array_flip(['bundle', 'order_number', 'text_block']));
    }

    foreach ($params as $field => $value) {
      $bao->$field = $value;
    }

    $bao->save();
    return $bao;
  }

  /**
   * Get the current field values of the instance
   *
   * @return array
   */
  public function fieldValues() {
    return array_intersect_key((array) $this, self::fields());
  }

  /**
   * Query SegmentationOrders by Campaign ID and SegmentationIndex IDs
   *
   * @param $campaign_id ID of campaign
   * @param $segment_ids list of segment orders to fetch
   *
   * @return array segment_id, name, bundle, text_block, order
   */
  public static function getSegmentationOrderByCampaignAndSegmentList($campaign_id, $segment_ids) {
    $campaign_id = (int) $campaign_id;
    $segment_id_list = implode(',', array_map('intval', $segment_ids));

    if (empty($segment_id_list)) return [];

    $query = CRM_Core_DAO::executeQuery("
      SELECT
        si.id           AS segment_id,
        so.id           AS segmentation_order_id,
        si.name         AS name,
        so.order_number AS order_number,
        so.bundle       AS bundle,
        so.text_block   AS text_block
      FROM civicrm_segmentation_order so
      INNER JOIN civicrm_segmentation_index si
        ON si.id = so.segment_id
      WHERE so.campaign_id = $campaign_id
        AND so.segment_id IN ($segment_id_list)
      ORDER BY so.order_number ASC, so.id ASC
    ");

    $segments = [];

    while ($query->fetch()) {
      $segments[$query->segment_id] = (array) $query;
    }

    return $segments;
  }

  /**
   * Check whether a segment name is in use in a campaign.
   *
   * Optionally, you can exclude a specific segment using $excluded_segment_id.
   *
   * @param $campaign_id
   * @param $name
   * @param null $excluded_segment_id
   *
   * @return bool whether the segment name is available
   */
  public static function isSegmentNameAvailable($campaign_id, $name, $excluded_segment_id = NULL) {
    $query = "
      SELECT COUNT(*) AS count
      FROM civicrm_segmentation_order so
      INNER JOIN civicrm_segmentation_index si
        ON si.id = so.segment_id
      WHERE so.campaign_id = %0
        AND si.name = %1
    ";

    $params = [
     [$campaign_id, 'Integer'],
     [$name, 'String'],
    ];

    if (!is_null($excluded_segment_id)) {
      $query .= " AND segment_id != %2";
      $params[] = [$excluded_segment_id, 'Integer'];
    }

    return 0 === (int) CRM_Core_DAO::singleValueQuery($query, $params);
  }

}
