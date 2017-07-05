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
 * @package     cleaner_muc
 * @subpackage  local_datacleaner
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cleaner_muc\dml;

defined('MOODLE_INTERNAL') || die();

/**
 * Class muc_config_db
 *
 * @package     cleaner_muc
 * @subpackage  local_datacleaner
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class muc_config_db {
    const TABLE_NAME = 'cleaner_muc_configs';

    public static function save($wwwroot, $configuration) {
        global $DB;

        static::delete($wwwroot);

        // The wwwroot is base64 encoded to prevent being washed during cleanup.
        $wwwroot64 = base64_encode($wwwroot);

        $data = (object)[
            'wwwroot'       => $wwwroot64,
            'configuration' => $configuration,
            'lastmodified'  => time(),
        ];

        return $DB->insert_record(self::TABLE_NAME, $data);
    }

    public static function get($wwwroot) {
        global $DB;

        $wwwroot64 = base64_encode($wwwroot);
        $config = $DB->get_field(self::TABLE_NAME, 'configuration', ['wwwroot' => $wwwroot64]);

        if ($config === false) {
            return null;
        }

        return $config;
    }

    public static function get_all() {
        global $DB;

        $rows = $DB->get_records(self::TABLE_NAME);

        $all = [];
        foreach ($rows as $row) {
            $all[base64_decode($row->wwwroot)] = $row->configuration;
        }

        ksort($all);

        return $all;
    }

    public static function delete($wwwroot) {
        global $DB;

        $wwwroot64 = base64_encode($wwwroot);
        $DB->delete_records(self::TABLE_NAME, ['wwwroot' => $wwwroot64]);
    }
}
