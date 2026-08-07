<?php
/**
 * app/Helpers/Positions.php
 * Central list of organizational position keys.
 * Labels are translated via lang files (position_<key>).
 * To add a new position: add the key here + translate it in every language file under lang/.
 */

const POSITION_KEYS = [
    'guardian', 'chairman', 'vice_chairman', 'gen_secretary', 'joint_secretary',
    'finance_secretary', 'info_secretary', 'media_secretary', 'it_secretary',
    'exec_member', 'volunteer', 'member',
];

/** Returns ['guardian' => 'سرپرست', ...] in the active language */
function positionOptions(): array
{
    $out = [];
    foreach (POSITION_KEYS as $key) {
        $out[$key] = t_raw('position_' . $key);
    }
    return $out;
}
