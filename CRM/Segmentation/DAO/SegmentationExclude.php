<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from de.systopia.segmentation/xml/schema/CRM/Segmentation/SegmentationExclude.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:a6d04e8bb7c0bbde1121ca6fbf06d277)
 */
use CRM_Segmentation_ExtensionUtil as E;

/**
 * Database access object for the SegmentationExclude entity.
 */
class CRM_Segmentation_DAO_SegmentationExclude extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_segmentation_exclude';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique SegmentationExclude ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * FK to Campaign
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $campaign_id;

  /**
   * FK to SegmentationIndex
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $segment_id;

  /**
   * FK to Contact
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $contact_id;

  /**
   * FK to Membership
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $membership_id;

  /**
   * Date/Time the exclusion was created
   *
   * @var string
   *   (SQL type: datetime)
   *   Note that values will be retrieved from the database as a string.
   */
  public $created_date;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_segmentation_exclude';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Segmentation Excludes') : E::ts('Segmentation Exclude');
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
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'campaign_id', 'civicrm_campaign', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'segment_id', 'civicrm_segmentation_index', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contact_id', 'civicrm_contact', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'membership_id', 'civicrm_membership', 'id');
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
          'description' => E::ts('Unique SegmentationExclude ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_segmentation_exclude.id',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'campaign_id' => [
          'name' => 'campaign_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Campaign ID'),
          'description' => E::ts('FK to Campaign'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_segmentation_exclude.campaign_id',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'FKClassName' => 'CRM_Campaign_DAO_Campaign',
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
          'where' => 'civicrm_segmentation_exclude.segment_id',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'FKClassName' => 'CRM_Segmentation_DAO_SegmentationIndex',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Contact ID'),
          'description' => E::ts('FK to Contact'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_segmentation_exclude.contact_id',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'membership_id' => [
          'name' => 'membership_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Membership ID'),
          'description' => E::ts('FK to Membership'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_segmentation_exclude.membership_id',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'FKClassName' => 'CRM_Member_DAO_Membership',
          'html' => [
            'type' => 'Number',
          ],
          'add' => NULL,
        ],
        'created_date' => [
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => E::ts('Created Date'),
          'description' => E::ts('Date/Time the exclusion was created'),
          'required' => FALSE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_segmentation_exclude.created_date',
          'default' => 'CURRENT_TIMESTAMP',
          'table_name' => 'civicrm_segmentation_exclude',
          'entity' => 'SegmentationExclude',
          'bao' => 'CRM_Segmentation_DAO_SegmentationExclude',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'segmentation_exclude', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'segmentation_exclude', $prefix, []);
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