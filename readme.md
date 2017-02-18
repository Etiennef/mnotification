# Presentation
This plugin intends to add some cutomisation to the way notificaitons behave in GLPI.
It has (for now) three completly independant features.

## Message on close
In GLPI, when you accept a solution, you have the possibility to add a message.
It will be added as a followup, as the ticket is closed.
Sadly, when you configure your notification template for a closed ticket, there isn't really a way to know if such a message has been added.
You could add the last followup message, but it means that mabye it's an old followup.

This plugin intends to help in that matter: when the 'close' notification event is passed, it checks if the last followup is more recent than the solution.
If yes, it means it's a message added when the ticket was closed.
The plugin then add a notification data usable from the notification template configuration : ```##mnotification.hasclosemsg##```

Nothing configurable here.

## Notification on Due date update
In GLPI, pretty much anything happening in the 'top part' of the ticket raises the 'update' notification event.
Somtimes, it's just too much.
You may want to notify for a particular update, but not for every updates.
In my case, it was for an update of the due date.

This plugin simply adds an event that is triggered in three cases:
* A due date has been set (when no date was set before)
* A due date has been removed (which means the due date was set before)
* The due date has been changed by more than a configurable amount

In those cases, the notification datas are enchiched by three entries:
* ```##mnotification.subevent## ``` which takes the values ```set```, ```remove``` or ```change``` (for the three cases defined just before)
* ```##mnotification.prev_duedate##``` which contains the formatted due date before the update
* ```##mnotification.new_duedate##``` which contains the formatted current due date

There are three configurations parameters possible for this feature:
* enable/disable it
* decide how much the due date has to change to trigger a notification => set in seconds, strongly advised >60 because of the way GLPI handles seconds in the due date
* the format used for the two added date notification datas => can be any valid Date->format string

## Filtered notification targets
In GLPI, the same user sometimes has several roles on a ticket, and if you have different notification templates for each role, it's hard to control which one these people will get.
In my case, it was annoying, because some templates say 'for your infirmation', other say 'now do something', which means that sometimes someone who is expected to do something will get a misleading 'for your information' message.

This plugin add some notification targets that correspond to the GLPI basic ones for roles on ticket, but with filters: Named requester user, user in requester group, ... same for observer and assigned.
Each of these groups contains the same people than the classic corresponding GLPI groups, minus those that belong to an other group (configurable).
For example, I can have a target that contains every named observer that is not also assigned to the ticket (which is exactly what I need in my 'for your information' example)

Also, the plugin adds a new target for assigned groups: it contains people that belong to assigned groups, except if the ticket is assigned to a user, in this case this target is empty. This additionnal group has two variants: 'classic' and 'filtered'. The filtered works the same way as explain previously.

It's designed to be used in a organisation where assigned groups represent people who may solve the ticket, but only one will effectively solve it. The one is suppose to assign the ticket to himself when he start treating it.
That way, any followup added before the ticket has been taken into account can be notified to everyone, but once someone treats it, there is no point in spamming everyone else in the assigned group.
There are certainly other possible use cases.

This features has teh following configurations:
* enable/disable it
* for each filtrable group, you can decide which roles you don't want (for example, you can setup your 'requester' filtered group not to contain observers, or not to contain assigned groups... you decide)

# Requirement
Like all my plugins, this plugin works only on GLPI 0.84 (mabye even less, it's tested only in GLPI 0.84.8), and requires Configmanager 1.x.x

If you'd like to use it on a more recent version, please let me know, I'd be glad to help. You would have to do the testing (well, i'll test on an almost-empty GLPI of the right version, which may be enough, but not on a real-world GLPI with real-world users)