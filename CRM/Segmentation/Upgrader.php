<?php

use CRM_Segmentation_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Segmentation_Upgrader extends CRM_Extension_Upgrader_Base {

  public function upgrade_0900() {
    $this->ctx->log->info('Updating segmentation schema to 0.9.0');
    $this->executeSqlFile('sql/create_activity_contact_segmentation.sql');
    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();
    return TRUE;
  }

  public function upgrade_0910() {
    $this->ctx->log->info('Updating segmentation schema to 0.9.1');

    $bundle_col_exists = (bool) CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM civicrm_segmentation_order WHERE Field = 'bundle'");

    if (!$bundle_col_exists) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_segmentation_order` ADD `bundle` varchar(255) NULL");
    }

    $text_block_col_exists = (bool) CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM civicrm_segmentation_order WHERE Field = 'text_block'");

    if (!$text_block_col_exists) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_segmentation_order` ADD `text_block` varchar(255) NULL");
    }

    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();
    return TRUE;
  }

  public function upgrade_0920() {
    $this->ctx->log->info('Updating segmentation schema to 0.9.2');
    $this->executeSqlFile('sql/create_segmentation_exclude.sql');
    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();
    return TRUE;
  }

  public function upgrade_0930() {
    $this->ctx->log->info('Updating segmentation schema to 0.9.3');
    $customData = new CRM_Utils_CustomData('de.systopia.segmentation');
    $customData->syncOptionGroup(__DIR__ . '/../../resources/activity_type_option_group.json');
    return TRUE;
  }

}
