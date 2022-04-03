<?php
define('Copito_i18l_GEN_MemCTL',"🛡 *MemCTL 2.3* (%1\$s)\n`$ %2\$s`\n\n");
define('Copito_i18l_GEN_Reenroll',"🐳 *Role Re-enroll* (%1\$s)\n`$ %2\$s`\n\n");

define('Copito_i18l_MOD_NotFound',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nUsuario %3\$s no encontrado.");
define('Copito_i18l_MOD_NotRoles_Junior',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nPermisos insuficientes - Requiere Mod. Junior o superior.");
define('Copito_i18l_MOD_NotRoles_Senior',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nPermisos insuficientes - Requiere Mod. Sénior o superior.");
define('Copito_i18l_MOD_NotRoles_Admin',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nPermisos insuficientes - Requiere Mod. Mem.");
define('Copito_i18l_MOD_NotRoleSet',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nPermisos insuficientes - El usuario %3\$s tiene un rango más elevado.");
define('Copito_i18l_MOD_Err_Time',Copito_i18l_GEN_MemCTL."*Error de ejecución*\n\nNo se ha especificado ningún número de tiempo válido.\nEjemplo de uso: `/ban @username 30m`");

define('Copito_i18l_MOD_Ban_Ok_Timed_Min',Copito_i18l_GEN_MemCTL."*Expulsar usuario del grupo*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s minuto(s).");
define('Copito_i18l_MOD_Ban_Ok_Timed_Hour',Copito_i18l_GEN_MemCTL."*Expulsar usuario del grupo*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s hora(s).");
define('Copito_i18l_MOD_Ban_Ok_Timed_Days',Copito_i18l_GEN_MemCTL."*Expulsar usuario del grupo*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s día(s).");
define('Copito_i18l_MOD_Ban_Ok',Copito_i18l_GEN_MemCTL."*Expulsar usuario del grupo*\n\n*Usuario:* %3\$s.\n*Duración:* INDEFINIDO.");
define('Copito_i18l_MOD_Unban_Ok',Copito_i18l_GEN_MemCTL."*Remover expulsión*\n\n*Usuario:* %3\$s.");
define('Copito_i18l_MOD_Kick_Ok',Copito_i18l_GEN_MemCTL."*Echar usuario del grupo*\n\n*Usuario:* %3\$s.");
define('Copito_i18l_MOD_Mute_Ok_Timed_Min',Copito_i18l_GEN_MemCTL."*Mutear usuario*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s minuto(s).");
define('Copito_i18l_MOD_Mute_Ok_Timed_Hour',Copito_i18l_GEN_MemCTL."*Mutear usuario*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s hora(s).");
define('Copito_i18l_MOD_Mute_Ok_Timed_Days',Copito_i18l_GEN_MemCTL."*Mutear usuario*\n\n*Usuario:* %3\$s.\n*Duración:* %4\$s día(s).");
define('Copito_i18l_MOD_Mute_Ok',Copito_i18l_GEN_MemCTL."*Mutear usuario*\n\n*Usuario:* %3\$s.\n*Duración:* INDEFINIDO.");
define('Copito_i18l_MOD_Unmute_Ok',Copito_i18l_GEN_MemCTL."*Remover muteo*\n\n*Usuario:* %3\$s.");
define('Copito_i18l_MOD_Strike_Ok',Copito_i18l_GEN_MemCTL."*Se aplica un strike*\n\n*Usuario:* %3\$s.\n*Strikes:* %4\$s strike(s).");
define('Copito_i18l_MOD_Strike_Chng',Copito_i18l_GEN_MemCTL."*Cambiar número de STRIKES*\n\n*Usuario:* %3\$s.\n*Strikes:* %4\$s strike(s).");
define('Copito_i18l_MOD_Strike_Reset',Copito_i18l_GEN_MemCTL."*RESETEAR número de STRIKES*\n\n*Usuario:* %3\$s.\n*Strikes:* %4\$s strike(s).");
define('Copito_i18l_MOD_Strike_Print',Copito_i18l_GEN_MemCTL."*CONSULTA número actual de STRIKES*\n\n*Usuario:* %3\$s.\n*Strikes:* %4\$s strike(s).");
define('Copito_i18l_MOD_Strike_Ban',Copito_i18l_GEN_MemCTL."*Se aplica un strike + expulsión*\n\n*Usuario:* %3\$s.\n*Strikes:* %4\$s strike(s).\n*Duración:* INDEFINIDO.");
define('Copito_i18l_MOD_Strike_List',Copito_i18l_GEN_MemCTL."*CONSULTA de usuarios con STRIKES*\n\n%3\$s");

define('Copito_i18l_CHT_Floody_Dispatch',Copito_i18l_GEN_MemCTL."*Control de Flood automático*\n\n*Usuario:* %3\$s.\n*Fluudy:* %4\$s minuto(s).");
define('Copito_i18l_CHT_Floody_Reset',Copito_i18l_GEN_MemCTL."*Reinciar Flood manualmente\n\n*Usuario:* %3\$s.\n*Fluudy:* RESET TRIGGERED.");

define('Copito_i18l_Reenroll_Refresh',Copito_i18l_GEN_Reenroll."*Permisos de usuario actualizados (corrección de incoherencia con el perfil interno detectada)*\n\n*Usuario:* %3\$s.\n\n*ROL:* %4\$s.");
define('Copito_i18l_Reenroll_Probes',Copito_i18l_GEN_Reenroll."*Título de usuario actualizado (corrección de incoherencia con el perfil interno detectada)*\n\n*Usuario:* %3\$s.\n\n*Título:* %4\$s.");



define('Copito_i18l_MOD_Err_Reply',"🛡 *MemCTL 2.2* (%1\$s)\n\n*Error de ejecución*\n\nNo se ha especificado ningún usuario.\nHazlo o bien escribiendo su @, nombre completo o ID; o respondiendo uno de sus mensajes.");

define('Copito_i18l_MOD_Help_Ban', "🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de expulsión*

Expulsar permanentemente o durante un periodo de tiempo determinado a un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes.

*+* Expulsar especificando sujeto y tiempo.
(el tiempo será especificado con un numeral y letra: d - días; h - horas; m - minutos;)
`/ban @usuario 2d`

*+* Expulsar especificando tiempo.
(respondiendo mensaje)
`/ban 2d`

*+* Expulsar PERMANENTEMENTE especificando sujeto.
`/ban @usuario`

*+* Expulsar PERMANENTEMENTE
(respondiendo mensaje)
`/ban`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
`/memctl ban`
con los mismos argumentos y misma funcionalidad.

También es posible ejecutar estos comandos con los siguientes alias:
`/expulsar`

");
define('Copito_i18l_MOD_Help_Unban',"🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de retirada de expulsión*

Retirada de expulsión para un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes.

*+* Retirar expulsión especificando sujeto.
`/unban @usuario`

*+* Retirar expulsión respondiendo mensaje.
(respondiendo mensaje)
`/unban`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
```/memctl unban```
con los mismos argumentos y misma funcionalidad.
");
define('Copito_i18l_MOD_Help_Kick',"🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de echada*

Echa a un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes. Debido a que la API de Bots de Telegram no ofrece ningún método para echar usuarios de un grupo, esta acción internamente ejecuta una expulsión de 5 minutos (tiempo mínimo permitido), por lo cual, trasncurridos estos, el usuario podrá volver a entrar nuevamente y/o visualizar los mensajes del grupo.

*+* Echar especificando sujeto
`/kick @usuario`

*+* Echar respondiendo mensaje.
(respondiendo mensaje)
`/kick`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
```/memctl kick```
con los mismos argumentos y misma funcionalidad.

También es posible ejecutar estos comandos con los siguientes alias:
`/echar`
");
define('Copito_i18l_MOD_Help_Mute', "🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de muteo*

Mutear permanentemente o durante un periodo de tiempo determinado a un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes.
El sujeto permanecerá en el grupo y leer los mensajes pero no podrá enviar mensajes.

*+* Mutear especificando sujeto y tiempo.
(el tiempo será especificado con un numeral y letra: d - días; h - horas; m - minutos;)
`/mute @usuario 2d`

*+* Mutear especificando tiempo.
(respondiendo mensaje)
`/mute 2d`

*+* Mutear PERMANENTEMENTE especificando sujeto.
`/mute @usuario`

*+* Mutear PERMANENTEMENTE
(respondiendo mensaje)
`/mute`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
`/memctl mute`
con los mismos argumentos y misma funcionalidad.

También es posible ejecutar estos comandos con los siguientes alias:
`/mutear`

");
define('Copito_i18l_MOD_Help_Unmute',"🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de retirada de muteo*

Retirada de muteo para un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes.

*+* Retirar muteo especificando sujeto.
`/unmute @usuario`

*+* Retirar muteo respondiendo mensaje.
(respondiendo mensaje)
`/unmute`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
```/memctl unmute```
con los mismos argumentos y misma funcionalidad.
");
define('Copito_i18l_MOD_Help_Delete',"🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de borrado de mensajes*

Retirada de un mensaje.

*+* Retirar mensaje respondiéndolo.
`/delete`

Se ofrece retrocompatibilidad con los comandos de la versión 1.0 de Copito, por tanto, es posible ejecutar
```/memctl delete```
con los mismos argumentos y misma funcionalidad.

También es posible ejecutar estos comandos con los siguientes alias:
`/remove`
`/memctl remove`
`/quitar`
`/borrar`
");
define('Copito_i18l_MOD_Help_Strike', "🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de gestión de STRIKES*

Aplicar un STRIKE a un usuario del grupo, especificando su @, nombre completo o ID; o bien, respondiendo uno de sus mensajes.
Todos los usuarios del grupo poseen un contador de STRIKES establecido a 0 por defecto. Al ejecutar este comando se les irá sumando dicho contador. Cuando un usuario llega a 3 STRIKE o más, será expulsado del grupo PERMANENTEMENTE. En caso de ejecutar este comando como Mod. Junior, el umbral de expulsión será 10 (y no 3).

*+* Reiniciar contador de STRIKES para un sujeto.
`/strike @usuario reset`

*+* Consultar número de STRIKES para un sujeto. Este comando es de consulta y no cambiará el contador.
`/strike @usuario print`

*+* Cambiar número de STRIKES del contador. El argumento extra ha de ser un numeral. Si este supera el umbral de expulsión, el usuario será expulsado.
`/strike @usuario 2`

*+* Aplicar STRIKE especificando sujeto
`/strike @usuario`

*+* Aplicar STRIKE respondiendo mensaje
(respondiendo mensaje)
`/strike`

*+* CONSULTA lista de los usuarios del grupo que tengan STRIKES.
`/strike list`

También es posible ejecutar estos comandos con los siguientes alias:
`/warn`
`/aviso`
");
define('Copito_i18l_MOD_Help', "🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de moderación*

Copito posee una serie de comandos para gestionar las expulsiones y muteos dentro del grupo (en la versión 1.0 estos comandos formaban parte del MEM [Mod's Extended Moderation]). Cada comando asi como sus diferentes argumentos están documentados mediante el uso del comando `/man`. A continuación, se muestra una lista con cada uno de ellos (y sus respectivos comandos de ayuda).

*+* *Expulsión*
Expulsar usuario del grupo, sea permanente o durante un periodo de tiempo determinado. 
(Véase `/man ban`)
`/ban`

*+* *Mutear*
Mutear usuario del grupo, sea permanente o durante un periodo de tiempo determinado. 
(Véase `/man mute`)
`/mute`

*+* *Retirar expulsión*
Retirar expulsión de un usuario previamente expulsado. 
(Véase `/man unban`)
`/unban`

*+* *Retirar muteo*
Retirar muteo de un usuario previamente muteado. 
(Véase `/man unmute`)
`/unmute`

*+* *Kickear*
Echa del grupo a un usuario (expulsión de 5 minutos) 
(Véase `/man kick`)
`/kick`

*+* *Strike*
Aplica un Strike a un usuario. A los 3 o 10, el usuario es expulsado PERMANENTEMENTE. También es posible consultar, resetear o mostrar una lista de los usuarios con strikes. 
(Véase `/man strike`)
`/strike`

*+* *Borrar mensaje*
Borra mensajes en el chat del grupo. 
(Véase `/man delete`)
`/delete`

Los comandos anteriores tienen definidos alias (p.ej. `/delete` acepta `/remove` como alias). Algunos comandos también ofrecen equivalencias con los comandos de la versión 1.0 (p. ej. `/memctl ban`). Para más información consultar documentación.
");

define('Copito_i18l_CHT_Floody_Help', "🛡 *MemCTL 2.2* (%1\$s)
`$ %2\$s`

*Ayuda para los comandos de FLOOD*

Controla automáticamente el flood del chat. (Comandos de uso interno)

*+* Flood Dispatch automático. (INTERNAL USE ONLY)
`/fluddy dispatch @usuario`

*+* Flood Dispatch automático.
(respondiendo mensaje)
`/fluddy dispatch`

");