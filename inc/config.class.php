<?php
include_once 'filteredtarget.class.php';

/**
 * Objet décrivant la configuration du plugin
 *
 * @author Etiennef
 */
class PluginMnotificationConfig extends PluginConfigmanagerConfig {

	static function makeConfigParams() {
		return array(
			'_title' => array(
				'type' => 'readonly text',
				'types' => array(self::TYPE_GLOBAL),
				'text' => self::makeHeaderLine(__('config.filtered_target.title', 'mnotification'))
			),
			'filter_target_active' => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 25,
				'text' => __('config.filtered_target.active', 'mnotification'),
				'values' => array(
					'1' => Dropdown::getYesNo('1'),
					'0' => Dropdown::getYesNo('0')
				),
			    'tooltip' => __('config.filtered_target.active.tootlip', 'mnotification'),
				'default' => '0'
			),
			PluginMnotificationFilteredtarget::filtered_user_requester => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_user_requester', 'mnotification'),
				'values' => array(
        		        'user_observer' => __('config.filtered_target.not_user_observer', 'mnotification'),
        		        'group_observers' => __('config.filtered_target.not_group_observers', 'mnotification'),
                        'user_technician' => __('config.filtered_target.not_user_technician', 'mnotification'),
                        'group_technicians' => __('config.filtered_target.not_group_technicians', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_group_requesters => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_group_requesters', 'mnotification'),
				'values' => array(
        		        'user_observer' => __('config.filtered_target.not_user_observer', 'mnotification'),
        		        'group_observers' => __('config.filtered_target.not_group_observers', 'mnotification'),
                        'user_technician' => __('config.filtered_target.not_user_technician', 'mnotification'),
                        'group_technicians' => __('config.filtered_target.not_group_technicians', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_user_observer => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_user_observer', 'mnotification'),
				'values' => array(
        		        'user_requester' => __('config.filtered_target.not_user_requester', 'mnotification'),
        		        'group_requesters' => __('config.filtered_target.not_group_requesters', 'mnotification'),
                        'user_technician' => __('config.filtered_target.not_user_technician', 'mnotification'),
                        'group_technicians' => __('config.filtered_target.not_group_technicians', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_group_observers => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_group_observers', 'mnotification'),
				'values' => array(
        		        'user_requester' => __('config.filtered_target.not_user_requester', 'mnotification'),
        		        'group_requesters' => __('config.filtered_target.not_group_requesters', 'mnotification'),
                        'user_technician' => __('config.filtered_target.not_user_technician', 'mnotification'),
                        'group_technicians' => __('config.filtered_target.not_group_technicians', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_user_technician => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_user_technician', 'mnotification'),
				'values' => array(
        		        'user_requester' => __('config.filtered_target.not_user_requester', 'mnotification'),
        		        'group_requesters' => __('config.filtered_target.not_group_requesters', 'mnotification'),
        		        'user_observer' => __('config.filtered_target.not_user_observer', 'mnotification'),
        		        'group_observers' => __('config.filtered_target.not_group_observers', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_group_technicians => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_group_technicians', 'mnotification'),
				'values' => array(
        		        'user_requester' => __('config.filtered_target.not_user_requester', 'mnotification'),
        		        'group_requesters' => __('config.filtered_target.not_group_requesters', 'mnotification'),
        		        'user_observer' => __('config.filtered_target.not_user_observer', 'mnotification'),
        		        'group_observers' => __('config.filtered_target.not_group_observers', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),
			PluginMnotificationFilteredtarget::filtered_group_techifnotech => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('targets.filtered_group_techifnotech', 'mnotification'),
				'values' => array(
        		        'user_requester' => __('config.filtered_target.not_user_requester', 'mnotification'),
        		        'group_requesters' => __('config.filtered_target.not_group_requesters', 'mnotification'),
        		        'user_observer' => __('config.filtered_target.not_user_observer', 'mnotification'),
        		        'group_observers' => __('config.filtered_target.not_group_observers', 'mnotification'),
				),
				'default' => '[]',
			 	'multiple' => true,
			 	'size' => 4,
			 	'mark_unmark_all' => true
			),


			'_title2' => array(
				'type' => 'readonly text',
				'types' => array(self::TYPE_GLOBAL),
				'text' => self::makeHeaderLine(__('config.change_duedate.title', 'mnotification'))
			),
			'change_duedate_event_active' => array(
				'type' => 'dropdown',
				'types' => array(self::TYPE_GLOBAL),
				'maxlength' => 25,
				'text' => __('config.change_duedate.active', 'mnotification'),
				'values' => array(
					'1' => Dropdown::getYesNo('1'),
					'0' => Dropdown::getYesNo('0')
				),
			    'tooltip' => __('config.change_duedate.active.tootlip', 'mnotification'),
				'default' => '0'
			),
			'change_due_date_mindiff' => array(
				'type' => 'text input',
				'types' => array(self::TYPE_PROFILE, self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('config.change_duedate.mindiff', 'mnotification'),
			    'tooltip' => __('config.change_duedate.mindiff.tootlip', 'mnotification'),
				'default' => '3600',
			),
			'change_due_date_format' => array(
				'type' => 'text input',
				'types' => array(self::TYPE_PROFILE, self::TYPE_GLOBAL),
				'maxlength' => 250,
				'text' => __('config.change_duedate.dateformat', 'mnotification'),
			    'tooltip' => __('config.change_duedate.format.tootlip', 'mnotification'),
				'default' => 'Y-m-d',
			),

		);
	}
}
?>


























