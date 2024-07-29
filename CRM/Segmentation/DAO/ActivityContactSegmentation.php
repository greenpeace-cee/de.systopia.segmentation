<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from de.systopia.segmentation/xml/schema/CRM/Segmentation/ActivityContactSegmentation.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:633a004cd585440ad27058cb1758badd)
 */
use CRM_Segmentation_ExtensionUtil as E;

/**
 * Database access object for the ActivityContactSegmentation entity.
 */
class CRM_Segmentation_DAO_ActivityContactSegmentation extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_activity_contact_segmentation';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique ActivityContactSegmentation ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * FK to ActivityContact
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $activity_contact_id;

  /**
   * FK to SegmentationIndex
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $segment_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_activity_contact_segmentation';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Activity Contact Segmentations') : E::ts('Activity Contact Segmentation');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'activity_contact_id', 'civicrm_activity_contact', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'segment_id', 'civicrm_segmentation_index', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'description' => E::ts('Unique ActivityContactSegmentation ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_activity_contact_segmentation.id',
          'table_name' => 'civicrm_activity_contact_segmentation',
          'entity' => 'ActivityContactSegmentation',
          'bao' => 'CRM_Segmentation_DAO_ActivityContactSegmentation',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'activity_contact_id' => [
          'name' => 'activity_contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Activity Contact ID'),
          'description' => E::ts('FK to ActivityContact'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_activity_contact_segmentation.activity_contact_id',
          'table_name' => 'civicrm_activity_contact_segmentation',
          'entity' => 'ActivityContactSegmentation',
          'bao' => 'CRM_Segmentation_DAO_ActivityContactSegmentation',
          'localizable' => 0,
          'FKClassName' => 'CRM_Activity_DAO_ActivityContact',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'segment_id' => [
          'name' => 'segment_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Segment ID'),
          'description' => E::ts('FK to SegmentationIndex'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_activity_contact_segmentation.segment_id',
          'table_name' => 'civicrm_activity_contact_segmentation',
          'entity' => 'ActivityContactSegmentation',
          'bao' => 'CRM_Segmentation_DAO_ActivityContactSegmentation',
          'localizable' => 0,
          'FKClassName' => 'CRM_Segmentation_DAO_SegmentationIndex',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'activity_contact_segmentation', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'activity_contact_segmentation', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}