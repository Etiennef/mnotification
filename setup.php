<?php

/**
 * Fonction de définition de la version du plugin
 * @return array description du plugin
 */
function plugin_version_mnotification() {
    return array(
        'name' => "Mnotification",
        'version' => '0.84+1.0.0',
        'author' => 'Etiennef',
        'license' => 'GPLv2+',
        'homepage' => 'https://github.com/Etiennef/mnotification',
        'minGlpiVersion' => '0.84'
    );
}

/**
 * Fonction de vérification des prérequis
 * @return boolean le plugin peut s'exécuter sur ce GLPI
 */
function plugin_mnotification_check_prerequisites() {
    if(version_compare(GLPI_VERSION, '0.84.8', 'lt') || version_compare(GLPI_VERSION, '0.85', 'ge')) {
        echo __("setup.wrongversion", 'mnotification');
        return false;
    }

    //Vérifie la présence de ConfigManager
    if(!(new Plugin())->isActivated('configmanager')) {
        echo __("setup.require.configmanager", 'mnotification');
        return false;
    }
    $configmanager_version = Plugin::getInfo('configmanager', 'version');
    if(version_compare($configmanager_version, '1.0.0', 'lt') || version_compare($configmanager_version, '2.0.0', 'ge')) {
        echo __("setup.require.configmanager", 'mnotification');
        return false;
    }

    return true;
}

/**
 * Fonction de vérification de la configuration initiale
 * @param type $verbose
 * @return boolean la config est faite
 */
function plugin_mnotification_check_config($verbose = false) {
    return true;
}

/**
 * Fonction d'initialisation du plugin.
 * @global array $PLUGIN_HOOKS
 */
function plugin_init_mnotification() {
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['mnotification'] = true;
    if(!(new Plugin())->isActivated('mnotification')) {
        return;
    }

    $conf = PluginMnotificationConfig::getConfigValues();

    //  Ajout d'une classe de config générique
    Plugin::registerClass('PluginMnotificationConfig', array('addtabon' => array(
            'Config',
    )));
    $PLUGIN_HOOKS['config_page']['mnotification'] = "../../front/config.form.php?forcetab=" . urlencode('PluginMnotificationConfig$1');


    Plugin::registerClass('PluginMnotificationFilteredtarget');

    // Add specific notification event (for change_duedate)
    $PLUGIN_HOOKS['item_get_events']['mnotification'] = array(
            'NotificationTargetTicket' => 'plugin_mnotification_add_events'
    );
    // Add hook to add new notificaiton targets (for filtered targets)
    $PLUGIN_HOOKS['item_add_targets']['mnotification'] = array(
            'NotificationTargetTicket' => 'plugin_mnotification_add_targets'
    );
    // Add hook to manage these targets (for filtered targets)
    $PLUGIN_HOOKS['item_action_targets']['mnotification'] = array(
            'NotificationTargetTicket' => 'plugin_mnotification_action_targets'
    );
    // Add hook to push data into notification templates (for change_duedate & msg on close)
    $PLUGIN_HOOKS['item_get_datas']['mnotification'] = array(
            'NotificationTargetTicket' => 'plugin_mnotification_add_datas'
    );

    // Add hook before ticket update to detect if 'input' due_date is different to current (for change_duedate)
    $PLUGIN_HOOKS['pre_item_update']['mnotification'] = array(
            'Ticket'       => 'plugin_mnotification_pre_item_update_ticket'
    );
    // Add hook after ticket update to raise change_due_date notification event if needed (for change_duedate)
    $PLUGIN_HOOKS['item_update']['mnotification'] = array(
            'Ticket'       => 'plugin_mnotification_item_update_ticket'
    );

}









