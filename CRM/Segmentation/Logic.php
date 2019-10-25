<?php
/*-------------------------------------------------------+
| SYSTOPIA Contact Segmentation Extension                |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/


/**
 * Basic configuration hub
 */
class CRM_Segmentation_Logic {

  /** cache all campaigns */
  private static $all_campaigns = NULL;

  /**
   * get the current order for the given campaign
   *
   * @param $campaign_id   int   campaign ID
   * @return array with segment_ids
   */
  public static function getSegmentOrder($campaign_id, $force_rebuild = FALSE) {
    $campaign_id = (int) $campaign_id;
    $segment_order = array();
    $query = CRM_Core_DAO::executeQuery("
            SELECT segment_id
            FROM civicrm_segmentation_order
            WHERE campaign_id = {$campaign_id}
            ORDER BY order_number ASC, id ASC");

    while ($query->fetch()) {
      $segment_order[] = $query->segment_id;
    }

    if (empty($segment_order) || $force_rebuild) {
      // maybe it wasn't stored yet, or somebody deleted it...
      $query = CRM_Core_DAO::executeQuery("
            SELECT DISTINCT(segment_id) AS segment_id
            FROM civicrm_segmentation
            WHERE campaign_id = {$campaign_id}");
      while ($query->fetch()) {
        $segment_order[] = $query->segment_id;
      }
      self::setSegmentOrder($campaign_id, $segment_order, TRUE);
    }

    return $segment_order;
  }

  /**
   * Set a new segment order for the given campaign
   *
   * @param $campaign_id   int   campaign ID
   * @param $segment_order array with segment_ids
   * @param $force update even if the current value is the same
   */
  public static function setSegmentOrder($campaign_id, $segment_order, $force = FALSE) {
    $campaign_id = (int) $campaign_id;
    // make sure we have a proper sequence
    $segment_order = array_values($segment_order);

    if ($force) {
      $current_segment_order = NULL; //doesn't matter
    } else {
      $current_segment_order = self::getSegmentOrder($campaign_id);
    }

    if ($force || $segment_order != $current_segment_order) {
      if (is_null($current_segment_order)) {
        CRM_Core_DAO::executeQuery(
          "DELETE FROM civicrm_segmentation_order WHERE campaign_id = %0",
          [[$campaign_id, 'Integer']]
        );
        $current_segment_order = [];
      }

      // iterate over existing segments
      foreach ($current_segment_order as $segment_id) {
        if (!in_array($segment_id, $segment_order)) {
          // segment is not in new order, delete
          CRM_Core_DAO::executeQuery(
            "DELETE FROM civicrm_segmentation_order WHERE campaign_id = %0 AND segment_id=%1",
            [[$campaign_id, 'Integer'], [$segment_id, 'Integer']]
          );
        }
        else {
          // segment is in new order, update order_number to current index
          CRM_Core_DAO::executeQuery(
            "UPDATE civicrm_segmentation_order SET order_number=%0 WHERE campaign_id = %1 AND segment_id=%2",
            [[array_search($segment_id, $segment_order) + 1, 'Integer'], [$campaign_id, 'Integer'], [$segment_id, 'Integer']]
          );
          unset($segment_order[array_search($segment_id, $segment_order)]);
        }
      }

      // remaining segments did not appear in previous order, add them
      foreach ($segment_order as $order_number => $segment_id) {
        CRM_Core_DAO::executeQuery(
          "INSERT IGNORE INTO civicrm_segmentation_order (campaign_id, segment_id, order_number) VALUES (%0, %1, %2)",
          [[$campaign_id, 'Integer'], [$segment_id, 'Integer'], [$order_number + 1, 'Integer']]
        );
      }
    }
  }

  /**
   * add the given segment to the campaign (if not already there)
   */
  public static function addSegmentToCampaign($segment_id, $campaign_id, $order_number = 1) {
    $segment_id = (int) $segment_id;
    $campaign_id = (int) $campaign_id;
    $order_number = (int) $order_number;
    CRM_Core_DAO::executeQuery("INSERT IGNORE INTO civicrm_segmentation_order (campaign_id,segment_id,order_number) VALUES ($segment_id, $campaign_id, $order_number)");
  }

  /**
   * will do the following:
   *   - Set the status to 'In Progress'
   *   - If the campaign's start date is in the past, it will be set to now
   *   - Freeze the contact segments, i.e. each contact is only linked with the highest segment as of the table below.
   */
  public static function startCampaign($campaign_id, $segment_order) {
    $campaign = civicrm_api3('Campaign', 'getsingle', array('campaign_id' => $campaign_id));
    if ($campaign['status_id'] != 1) {
      throw new Exception("Only campaigns with status 'planned' [1] can be started.");
    }

    // call another function to consolidate
    self::setSegmentOrder($campaign_id, $segment_order);
    self::consolidateSegments($campaign_id, $segment_order);

    // finally: update campaign
    $campaign_update = array(
      'id'        => $campaign_id,
      'status_id' => 2);
    if (  empty($campaign['start_date'])
       || strtotime($campaign['start_date']) < strtotime('now')) {
      $campaign_update['start_date'] = date('YmdHis');
    }
    civicrm_api3('Campaign', 'create', $campaign_update);
  }

  /**
   * delete all inferior (according to the currently given order) entries for contacts
   */
  protected static function consolidateSegments($campaign_id, $segment_order) {
    $timestamp = microtime(TRUE);
    $segments_settled = array();
    foreach ($segment_order as $segment_id) {
      if (!empty($segments_settled)) {
        // for each step delete the contact entries that already
        //  in one of the settled segments
        $segments_settled_list = implode(',', $segments_settled);
        CRM_Core_DAO::executeQuery("
          DELETE FROM civicrm_segmentation
          WHERE civicrm_segmentation.campaign_id = {$campaign_id}
            AND civicrm_segmentation.segment_id  = {$segment_id}
            AND civicrm_segmentation.entity_id IN (SELECT * FROM
                  (SELECT settled.entity_id
                     FROM civicrm_segmentation settled
                    WHERE settled.campaign_id = {$campaign_id}
                      AND settled.segment_id IN ({$segments_settled_list})
                  ) tmpdata )");

      }
      $segments_settled[] = $segment_id;
    }

    $segments_to_exclude = array(0);
    foreach ($segment_order as $segment_id) {
      $exclude_list = implode(',', $segments_to_exclude);
      $segment_count = CRM_Core_DAO::singleValueQuery("
        SELECT COUNT(DISTINCT(positive.entity_id))
        FROM civicrm_segmentation positive
        LEFT JOIN civicrm_segmentation negative ON positive.entity_id = negative.entity_id
                                               AND negative.campaign_id = {$campaign_id}
                                               AND negative.segment_id IN ({$exclude_list})
        WHERE positive.segment_id = {$segment_id}
          AND positive.campaign_id = {$campaign_id}
          AND negative.segment_id IS NULL");
      $segment_counts[$segment_id] = $segment_count;
      $segments_to_exclude[] = $segment_id;
    }
    $runtime = microtime(TRUE) - $timestamp;
    error_log("Segmentation::consolidateSegments took {$runtime}s");
    return $segment_counts;
  }

  /**
   * In some cases (e.g. migration, loss, etc, the campaign might not have a
   * segmentation order yet - which breaks some functions
   *
   * This method will make sure that in this case a segment order is generated
   */
  public static function verifySegmentOrder($campaign_id) {
    $campaign_id = (int) $campaign_id;
    // not good enough (GP-737):
    //    "SELECT count(segment_id) FROM civicrm_segmentation_order WHERE campaign_id = {$campaign_id}"
    // better:
    $test = CRM_Core_DAO::singleValueQuery("
      SELECT civicrm_segmentation.entity_id
      FROM civicrm_segmentation
      LEFT JOIN civicrm_segmentation_order ON civicrm_segmentation_order.campaign_id = civicrm_segmentation.campaign_id
                                          AND civicrm_segmentation_order.segment_id = civicrm_segmentation.segment_id
      WHERE civicrm_segmentation.campaign_id = {$campaign_id}
        AND civicrm_segmentation_order.order_number IS NULL
      LIMIT 1");
    if ($test) {
      self::getSegmentOrder($campaign_id, TRUE);
    }
  }

  /**
   * get a list segment_id -> contact_count for the given campaign
   *
   */
  public static function getSegmentCounts($campaign_id, $segment_order = array()) {
    self::verifySegmentOrder($campaign_id);
    $timestamp = microtime(TRUE);
    $campaign_id = (int) $campaign_id;
    $segment_counts = array();
    foreach ($segment_order as $segment_id) {
      $segment_counts[$segment_id] = 0;
    }

    $query = CRM_Core_DAO::executeQuery("
      SELECT
        civicrm_segmentation_order.segment_id AS segment_id,
        COUNT(DISTINCT(tmp.contact_id))       AS contact_count
      FROM (
        SELECT
          civicrm_segmentation.entity_id               AS contact_id,
          civicrm_segmentation.segment_id              AS segment_id,
          MIN(civicrm_segmentation_order.order_number) AS segment_order
        FROM civicrm_segmentation
        LEFT JOIN civicrm_segmentation_order ON civicrm_segmentation_order.campaign_id = civicrm_segmentation.campaign_id
                                            AND civicrm_segmentation_order.segment_id = civicrm_segmentation.segment_id
        WHERE civicrm_segmentation.campaign_id = {$campaign_id}
        GROUP BY civicrm_segmentation.entity_id) tmp
      LEFT JOIN civicrm_segmentation_order ON civicrm_segmentation_order.order_number = tmp.segment_order AND civicrm_segmentation_order.campaign_id = {$campaign_id}
      GROUP BY tmp.segment_order");
    while ($query->fetch()) {
      $segment_counts[$query->segment_id] = $query->contact_count;
    }

    $runtime = microtime(TRUE) - $timestamp;
    error_log("Segmentation::getSegmentCounts took {$runtime}s");

    return $segment_counts;
  }

  /**
   * get a list segment_id -> contact_count for the given campaign
   *
   */
  public static function getExcludedCounts($campaign_id, $segment_order = array()) {
    self::verifySegmentOrder($campaign_id);
    $timestamp = microtime(TRUE);
    $campaign_id = (int) $campaign_id;
    $segment_counts = array();
    foreach ($segment_order as $segment_id) {
      $segment_counts[$segment_id] = 0;
    }

    $query = CRM_Core_DAO::executeQuery("
      SELECT
        segment_id,
        COUNT(DISTINCT(contact_id)) AS contact_count
      FROM civicrm_segmentation_exclude
      WHERE campaign_id = %0
      GROUP BY segment_id", [[$campaign_id, 'Integer']]);
    while ($query->fetch()) {
      $segment_counts[$query->segment_id] = $query->contact_count;
    }

    $runtime = microtime(TRUE) - $timestamp;
    error_log("Segmentation::getExcludedCounts took {$runtime}s");

    return $segment_counts;
  }

  /**
   * get a list segment_id -> segment_title for the given segment ids
   *
   * @todo move to another class
   */
  public static function getSegmentTitles($segment_ids) {
    $segment_id_list = implode(',', array_map('intval', $segment_ids));
    if (empty($segment_id_list)) return array();

    $query = CRM_Core_DAO::executeQuery("
      SELECT
        id    AS segment_id,
        name  AS segment_title
      FROM civicrm_segmentation_index
      WHERE id IN ({$segment_id_list})");

    $segment_titles = array();
    while ($query->fetch()) {
      $segment_titles[$query->segment_id] = $query->segment_title;
    }
    return $segment_titles;
  }

  /**
   * Get a list of all campaigns
   */
  public static function getAllCampaigns() {
    if (self::$all_campaigns == NULL) {
      self::$all_campaigns = array();
      $campaign_query = civicrm_api3('Campaign', 'get', array(
        'option.limit' => 0,
        'is_active'    => 1,
        'return'       => 'id,title'
        ));
      foreach ($campaign_query['values'] as $campaign) {
        self::$all_campaigns[$campaign['id']] = $campaign['title'];
      }
    }
    return self::$all_campaigns;
  }

  /**
   * Take the given $campaign_ids and extend it by all it's subcampaigns
   */
  public static function includeSubcampaigns($campaign_ids) {
    $new_id_count = count($campaign_ids);
    $id_heap = array();
    foreach ($campaign_ids as $campaign_id) {
      $id_heap[$campaign_id] = 1;
    }

    while ($new_id_count > 0) {
      $new_id_count = 0;
      $id_list = implode(',', array_keys($id_heap));
      $query_sql = "SELECT DISTINCT(id) AS campaign_id
                    FROM civicrm_campaign
                    WHERE parent_id IN ({$id_list})
                      AND id NOT IN ({$id_list})";
      $query = CRM_Core_DAO::executeQuery($query_sql);
      while ($query->fetch()) {
        if (!isset($id_heap[$query->campaign_id])) {
          $id_heap[$query->campaign_id] = 1;
          $new_id_count += 1;
        }
      }
    }

    return array_keys($id_heap);
  }

  /**
   * Get a list of all campaigns
   */
  public static function getAllSegments() {
    $all_segments = array();
    $query = CRM_Core_DAO::executeQuery("SELECT id AS segment_id, name AS segment_name FROM civicrm_segmentation_index;");
    while ($query->fetch()) {
      $all_segments[$query->segment_id] = $query->segment_name;
    }
    return $all_segments;
  }

  /**
   * Add segments for a mass activity. Only use if campaign has a consolidated
   * segment order (@see CRM_Segmentation_Logic::consolidateSegments())
   *
   * @param $activity_id int
   * @param $campaign_id int
   */
  public static function addSegmentForMassActivity($activity_id, $campaign_id) {
    $query = "INSERT IGNORE INTO civicrm_activity_contact_segmentation (activity_contact_id, segment_id)
                   (SELECT
                      civicrm_activity_contact.id,
                      civicrm_segmentation.segment_id
                    FROM civicrm_segmentation
                    INNER JOIN civicrm_activity_contact ON civicrm_segmentation.entity_id=civicrm_activity_contact.contact_id
                    WHERE activity_id = %0
                      AND campaign_id = %1)";
    CRM_Core_DAO::executeQuery($query, [
      [$activity_id, 'Integer'],
      [$campaign_id, 'Integer'],
    ]);
  }

  /**
   * Add exclusion segment for a mass activity.
   *
   * @param $activity_id int
   * @param $campaign_id int
   */
  public static function addExclusionSegmentForMassActivity($activity_id, $campaign_id) {
    $query = "INSERT IGNORE INTO civicrm_activity_contact_segmentation (activity_contact_id, segment_id)
                   (SELECT
                      civicrm_activity_contact.id,
                      civicrm_segmentation_exclude.segment_id
                    FROM civicrm_segmentation_exclude
                    INNER JOIN civicrm_activity_contact ON civicrm_segmentation_exclude.contact_id=civicrm_activity_contact.contact_id
                    WHERE activity_id = %0
                      AND campaign_id = %1)";
    CRM_Core_DAO::executeQuery($query, [
      [$activity_id, 'Integer'],
      [$campaign_id, 'Integer'],
    ]);
  }

  /**
   * Add segment to an existing ActivityContact. Skips if a segment is
   * already assigned or if activity hasn't been assigned to contact at all.
   *
   * @param $activity_id int
   * @param $contact_id int
   */
  public static function addSegmentForActivityContact($activity_id, $contact_id) {
    $query = "INSERT IGNORE INTO civicrm_activity_contact_segmentation (activity_contact_id, segment_id)
                   (SELECT
                      civicrm_activity_contact.id,
                      civicrm_segmentation.segment_id
                    FROM civicrm_activity_contact
                    INNER JOIN civicrm_activity ON civicrm_activity.id=civicrm_activity_contact.activity_id
                    INNER JOIN civicrm_segmentation ON civicrm_segmentation.entity_id=civicrm_activity_contact.contact_id
                                                   AND civicrm_segmentation.campaign_id=civicrm_activity.campaign_id
                    LEFT JOIN civicrm_segmentation_order ON civicrm_segmentation_order.segment_id=civicrm_segmentation.segment_id
                                                        AND civicrm_segmentation_order.campaign_id=civicrm_segmentation.campaign_id
                    WHERE civicrm_activity_contact.activity_id = %0
                      AND civicrm_activity_contact.contact_id = %1
                    ORDER BY order_number
                    LIMIT 1)";
    CRM_Core_DAO::executeQuery($query, [
      [$activity_id, 'Integer'],
      [$contact_id, 'Integer'],
    ]);
  }

  /**
   * Make space for new segments: Move each segment with order_number > the
   * segment being split n steps down, where n is the number of buckets to be
   * created - 1 (since the segment being split already has the right order)
   *
   * @param $segmentCount
   * @param $campaignId
   * @param $orderNumber
   */
  public static function moveSegmentOrderNumber($segmentCount, $campaignId, $orderNumber) {
    CRM_Core_DAO::executeQuery(
      "UPDATE civicrm_segmentation_order
    SET order_number = order_number + %0
    WHERE civicrm_segmentation_order.campaign_id=%1 AND order_number > %2",
      [
        [$segmentCount - 1, 'Integer'],
        [$campaignId, 'Integer'],
        [$orderNumber, 'Integer'],
      ]
    );
  }

  /**
   * Convert percent of Contact to count of Contact
   *
   * @param $newSegments
   * @param $sumCountOfContact
   * @return array
   */
  public static function convertPercentToCountOfContact($newSegments, $sumCountOfContact) {
    $sum = 0;
    foreach ($newSegments as $key => $newSegment) {
      $absoluteContactCount = $newSegment['percent'] * $sumCountOfContact / 100;
      $newSegments[$key]['number'] = floor($absoluteContactCount);
      $newSegments[$key]['number_coefficient'] = $absoluteContactCount- floor($absoluteContactCount);
      $sum += floor($absoluteContactCount);
    }

    $countOfMissedContacts = $sumCountOfContact - $sum;
    if ($countOfMissedContacts !== 0) {
      for ($i = 0; $i < $countOfMissedContacts; $i++) {
        $key = static::findKeyWithTheBiggestCoefficient($newSegments);
        $newSegments[$key]['number'] = $newSegments[$key]['number'] + 1;
        $newSegments[$key]['number_coefficient'] = $newSegments[$key]['number_coefficient'] - 1;
      }
    }

    foreach ($newSegments as $key => $newSegment) {
      unset($newSegments[$key]['number_coefficient']);
    }

    return $newSegments;
  }

  /**
   * Finds key of array where the biggest coefficient
   *
   * @param $newSegments
   * @return int
   */
  private static function findKeyWithTheBiggestCoefficient($newSegments) {
    $maxValue = 0;
    $maxValueKey = 0;

    foreach ($newSegments as $key => $newSegment) {
      if ($newSegment['number_coefficient'] > $maxValue) {
        $maxValue = $newSegment['number_coefficient'];
        $maxValueKey = $key;
      }
    }

    return $maxValueKey;
  }

}
