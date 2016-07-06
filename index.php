<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Environment bar setup page.
 *
 * @package   local_envbar
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

admin_externalpage_setup('local_envbar');

$records = envbar_get_records();
$form = new \local_envbar\form\config(null, array('records' => $records));

if ($data = $form->get_data()) {

    local_envbar_setprodwwwroot($data->prodwwwroot);

    if (!empty($data->id)) {

        $keys = array_keys($data->id);

        foreach ($keys as $key => $value) {
            $item = new stdClass();
            $item->id = $data->id[$value];
            $item->colourbg = $data->colourbg[$value];
            $item->colourtext = $data->colourtext[$value];
            $item->matchpattern = $data->matchpattern[$value];
            $item->showtext = $data->showtext[$value];
            $item->enabled = $data->enabled[$value];

            // Do not update the database with manual set config.php items.
            if (!empty($data->locked[$value])) {
                continue;
            }

            if ($data->delete[$value] == 1) {
                delete_envbar($value);
            } else {
                // Update an item as the id has been set.
                update_envbar($item);
            }
        }
    }

    if (!empty($data->repeatid)) {
        $repeats = array_keys($data->repeatid);

        foreach ($repeats as $key => $value) {
            $item = new stdClass();
            // ID, $item->id not set.
            $item->colourbg = $data->repeatcolourbg[$value];
            $item->colourtext = $data->repeatcolourtext[$value];
            $item->matchpattern = $data->repeatmatchpattern[$value];
            $item->showtext = $data->repeatshowtext[$value];
            $item->enabled = $data->repeatenabled[$value];

            update_envbar($item);
        }
    }

    redirect(new moodle_url('/local/envbar/index.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header_envbar', 'local_envbar'));
echo get_string('help', 'local_envbar');
echo $form->display();
echo $OUTPUT->footer();

