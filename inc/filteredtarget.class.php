<?php
/**
 * Objet décrivant la configuration du plugin
 *
 * @author Etiennef
 */
class PluginMnotificationFilteredtarget extends CommonGLPI {
    const group_techifnotech = 1001;
    const filtered_group_requesters = 1002;
    const filtered_group_observers = 1003;
    const filtered_group_technicians = 1004;
    const filtered_group_techifnotech = 1005;
    const filtered_user_requester = 1006;
    const filtered_user_observer = 1007;
    const filtered_user_technician = 1008;

    static function isPluginTarget($targetid) {
        return $targetid>=1001 && $targetid<=1008;
    }

    static function getTargets() {
        return array(
                self::group_techifnotech => __('targets.group_techifnotech', 'mnotification'),
                self::filtered_group_requesters => __('targets.filtered_group_requesters', 'mnotification'),
                self::filtered_group_observers => __('targets.filtered_group_observers', 'mnotification'),
                self::filtered_group_technicians => __('targets.filtered_group_technicians', 'mnotification'),
                self::filtered_group_techifnotech => __('targets.filtered_group_techifnotech', 'mnotification'),
                self::filtered_user_requester => __('targets.filtered_user_requester', 'mnotification'),
                self::filtered_user_observer => __('targets.filtered_user_observer', 'mnotification'),
                self::filtered_user_technician => __('targets.filtered_user_technician', 'mnotification'),
        );
    }

    static function getUsersForNotif(NotificationTargetTicket $target) {
        $conf = PluginMnotificationConfig::getConfigValues();
        $notiftarget_id = $target->data['items_id'];

        // on réccupère les utilisateurs à notifier
        switch($notiftarget_id) {
            case self::filtered_group_requesters :
                $list = self::getLinkedGroupsByType($target, CommonITILActor::REQUESTER);
                break;
            case self::filtered_group_observers :
                $list = self::getLinkedGroupsByType($target, CommonITILActor::OBSERVER);
                break;
            case self::filtered_group_technicians :
                $list = self::getLinkedGroupsByType($target, CommonITILActor::ASSIGN);
                break;
            case self::group_techifnotech :
            case self::filtered_group_techifnotech :
                if(empty($target->obj->getUsers(CommonITILActor::ASSIGN))) {
                    $list = self::getLinkedGroupsByType($target, CommonITILActor::ASSIGN);
                } else {
                    return array();
                }
                break;
            case self::filtered_user_requester :
                $list = self::getLinkedUsersByType($target, CommonITILActor::REQUESTER);
                break;
            case self::filtered_user_observer :
                $list = self::getLinkedUsersByType($target, CommonITILActor::OBSERVER);
                break;
            case self::filtered_user_technician :
                $list = self::getLinkedUsersByType($target, CommonITILActor::ASSIGN);
                break;
            default:
                return array();
        }

        // On filtre la liste obtenue
        if ($notiftarget_id != self::group_techifnotech) {
            $exclude = array();
            if(in_array('user_requester', $conf[$notiftarget_id])) {
                self::getLinkedUsersByType($target, CommonITILActor::REQUESTER, $exclude);
            }
            if(in_array('group_requesters', $conf[$notiftarget_id])) {
                self::getLinkedGroupsByType($target, CommonITILActor::REQUESTER, $exclude);
            }
            if(in_array('user_observer', $conf[$notiftarget_id])) {
                self::getLinkedUsersByType($target, CommonITILActor::OBSERVER, $exclude);
            }
            if(in_array('group_observers', $conf[$notiftarget_id])) {
                self::getLinkedGroupsByType($target, CommonITILActor::OBSERVER, $exclude);
            }
            if(in_array('user_technician', $conf[$notiftarget_id])) {
                self::getLinkedUsersByType($target, CommonITILActor::ASSIGN, $exclude);
            }
            if(in_array('group_technicians', $conf[$notiftarget_id])) {
                self::getLinkedGroupsByType($target, CommonITILActor::ASSIGN, $exclude);
            }

            $list = array_diff_key($list, $exclude);
        }

        return $list;
    }

    /**
     * Cette fonction réccupère les utilisateurs appartenant à des groupes notifiables jouant un rôle sur ce ticket.
     * Largement inspiré de NotificationTargetCommonITILObject->getLinkedGroupsByType et NotificationTarget->getAddressesByGroup, à ceci près que les résultats sont mis dans un tableau plutôt que stockés dans l'objet NotificationTarget
     * @param NotificationTargetTicket $target
     * @param unknown $type CommonITILActor::REQUESTER, CommonITILActor::OBSERVER ou CommonITILActor::ASSIGN
     * @param array $output tableau de sortie (modifié par effet de bord et retourné)
     * @return [] tableau de données injectables dans NotificationTargetCommonITILObject->addToAddressesList
     */
    private static function getLinkedGroupsByType(NotificationTargetTicket $target, $type, &$output=array()) {
        global $DB;

        $grouplinktable = getTableForItemType($target->obj->grouplinkclass);
        $fkfield        = $target->obj->getForeignKeyField();

        $query = $target->getDistinctUserSql()."
            FROM `glpi_groups_users`
            INNER JOIN `glpi_users` ON (`glpi_groups_users`.`users_id` = `glpi_users`.`id`) ".
                $target->getProfileJoinSql()."
                INNER JOIN `glpi_groups` ON (`glpi_groups_users`.`groups_id` = `glpi_groups`.`id`)
                INNER JOIN `$grouplinktable`
                ON `$grouplinktable`.`groups_id` = `glpi_groups`.`id`
                AND `glpi_groups`.`is_notify`
                AND `$grouplinktable`.`$fkfield` = '".$target->obj->fields["id"]."'
                AND `$grouplinktable`.`type` = '$type'";

        foreach ($DB->request($query) as $data) {
            $output[$data['users_id']] = $data;
        }
        return $output;
    }

    /**
     * Cette fonction réccupère les utilisateurs notifiables jouant un rôle sur ce ticket.
     * Copier-collé légèrement modifié de NotificationTargetCommonITILObject->getLinkedUserByType, à ceci près que les résultats sont mis dans un tableau plutôt que stockés dans l'objet NotificationTarget
     * @param NotificationTargetTicket $target
     * @param unknown $type CommonITILActor::REQUESTER, CommonITILActor::OBSERVER ou CommonITILActor::ASSIGN
     * @param array $output tableau de sortie (modifié par effet de bord et retourné)
     * @return [] tableau de données injectables dans NotificationTargetCommonITILObject->addToAddressesList
     */
    private static function getLinkedUsersByType(NotificationTargetTicket $target, $type, &$output = array()) {
        global $DB, $CFG_GLPI;

        $userlinktable = getTableForItemType($target->obj->userlinkclass);
        $fkfield = $target->obj->getForeignKeyField();

        //Look for the user by his id
        $query = $target->getDistinctUserSql() . ",
        `$userlinktable`.`use_notification` AS notif,
        `$userlinktable`.`alternative_email` AS altemail
        FROM `$userlinktable`
        LEFT JOIN `glpi_users` ON (`$userlinktable`.`users_id` = `glpi_users`.`id`)" . $target->getProfileJoinSql() . "
        WHERE `$userlinktable`.`$fkfield` = '" . $target->obj->fields["id"] . "'
        AND `$userlinktable`.`type` = '$type'";

        foreach ( $DB->request($query) as $data ) {
            //Add the user email and language in the notified users list
            if ($data['notif']) {
                $author_email = UserEmail::getDefaultForUser($data['users_id']);
                $author_lang = $data["language"];
                $author_id = $data['users_id'];

                if (!empty($data['altemail']) && ($data['altemail'] != $author_email) && NotificationMail::isUserAddressValid($data['altemail'])) {
                    $author_email = $data['altemail'];
                }
                if (empty($author_lang)) {
                    $author_lang = $CFG_GLPI["language"];
                }
                if (empty($author_id)) {
                    $author_id = -1;
                }

                $output[$data['users_id']] = array(
                        'email' => $author_email,
                        'language' => $author_lang,
                        'users_id' => $author_id
                );
            }
        }

        // Anonymous user
        $query = "SELECT `alternative_email`
        FROM `$userlinktable`
        WHERE `$userlinktable`.`$fkfield` = '" . $target->obj->fields["id"] . "'
        AND `$userlinktable`.`users_id` = 0
        AND `$userlinktable`.`use_notification` = 1
        AND `$userlinktable`.`type` = '$type'";
        foreach ( $DB->request($query) as $data ) {
            if (NotificationMail::isUserAddressValid($data['alternative_email'])) {
                $output[$data['users_id']] = array(
                        'email' => $data['alternative_email'],
                        'language' => $CFG_GLPI["language"],
                        'users_id' => -1
                );
            }
        }
        return $output;
    }


}



?>