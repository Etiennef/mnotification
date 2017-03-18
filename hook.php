<?php

/**
 * Fonction d'installation du plugin
 * @return boolean
 */
function plugin_mnotification_install() {
	include 'inc/config.class.php';
    PluginMnotificationConfig::install();
	return true;
}

/**
 * Fonction de désinstallation du plugin
 * @return boolean
 */
function plugin_mnotification_uninstall() {
	include 'inc/config.class.php';
    PluginMnotificationConfig::uninstall();
	return true;
}

function plugin_mnotification_add_events(NotificationTargetTicket $target) {
    $conf = PluginMnotificationConfig::getConfigValues();
    if($conf['change_duedate_event_active']) {
        $target->events['plugin_mnotification_change_due_date'] = __("event.change_due_date", 'mnotification');
    }
}

function plugin_mnotification_add_targets(NotificationTargetTicket $target) {
    $conf = PluginMnotificationConfig::getConfigValues();
    if($conf['filter_target_active']) {
        foreach(PluginMnotificationFilteredtarget::getTargets() as $k => $l) {
            $target->addTarget($k, $l);
        }
    }
}

function plugin_mnotification_action_targets(NotificationTargetTicket $target) {
    $conf = PluginMnotificationConfig::getConfigValues();
    if($conf['filter_target_active'] && PluginMnotificationFilteredtarget::isPluginTarget($target->data['items_id'])) {
        $list = PluginMnotificationFilteredtarget::getUsersForNotif($target);
        foreach($list as $data) {
            $target->addToAddressesList($data);
        }
    }
}

function plugin_mnotification_add_datas(NotificationTargetTicket $target) {
    $conf = PluginMnotificationConfig::getConfigValues();
    /* @var $ticket Ticket */
    $ticket = $target->obj;

    // Add datas allowing to detect if ticket was closed with or without message
    if($target->raiseevent === 'closed') {

        $restrict = "`tickets_id`='".$ticket->getField('id')."'";
        $restrict .= " ORDER BY `date` DESC, `id` ASC";
        $restrict .= " LIMIT 1";

        $followups = getAllDatasFromTable('glpi_ticketfollowups',$restrict);

        if(empty($followups)) {
            $target->datas['##mnotification.hasclosemsg##'] = false;
        } else {
            $lastfollowupdate = reset($followups)['date'];
            $solvedate = $ticket->getField('solvedate');
            $target->datas['##mnotification.hasclosemsg##'] = $solvedate < $lastfollowupdate;
        }
    }

    // Add datas for change_duedate_event
    if($conf['change_duedate_event_active'] && $target->raiseevent === 'plugin_mnotification_change_due_date') {
        $target->datas['##mnotification.subevent##'] = $ticket->input['plugin_mnotification_change_due_date']['event'];
        $target->datas['##mnotification.prev_duedate##'] = $ticket->input['plugin_mnotification_change_due_date']['prev'];
        $target->datas['##mnotification.new_duedate##'] = $ticket->input['plugin_mnotification_change_due_date']['new'];
    }

}



function plugin_mnotification_pre_item_update_ticket($ticket) {
    $conf = PluginMnotificationConfig::getConfigValues();

    if ($conf['change_duedate_event_active'] && isset($ticket->input['due_date']) && $ticket instanceof Ticket) {
        $current = $ticket->getField('due_date') === null ? 'NULL' : $ticket->getField('due_date');
        $new = $ticket->input['due_date'];

        if($current === 'NULL' && $new !== 'NULL') {
            $ticket->input['plugin_mnotification_change_due_date'] = array(
                    'event' => 'set',
                    'prev' => $current,
                    'new' => (new DateTime($new))->format($conf['change_due_date_format']),
            );
        } else if($current !== 'NULL' && $new === 'NULL') {
            $ticket->input['plugin_mnotification_change_due_date'] = array(
                    'event' => 'remove',
                    'prev' => (new DateTime($current))->format($conf['change_due_date_format']),
                    'new' => $new,
            );
        } else if($current !== $new) {
            // Raise event only if diff greater than $conf['change_due_date_mindiff'] (in seconds)
            if(abs((new DateTime($current))->getTimestamp() - (new DateTime($new))->getTimestamp()) > $conf['change_due_date_mindiff']) {
                $ticket->input['plugin_mnotification_change_due_date'] = array(
                        'event' => 'change',
                        'prev' => (new DateTime($current))->format($conf['change_due_date_format']),
                        'new' => (new DateTime($new))->format($conf['change_due_date_format']),
                );
            }
        }

    }
}

function plugin_mnotification_item_update_ticket($ticket) {
    $conf = PluginMnotificationConfig::getConfigValues();
    if ($conf['change_duedate_event_active'] && $ticket instanceof Ticket) {
        if(isset($ticket->input['plugin_mnotification_change_due_date'])) {
            NotificationEvent::raiseEvent('plugin_mnotification_change_due_date', $ticket);
        }
    }
}













