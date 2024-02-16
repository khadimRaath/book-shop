<?php

namespace bookshop\includes;

/**
 * Sale Version comparison class
 */
class VersionCompare
{
    public function getTimeZoneByVersion($version)
    {
        if (version_compare($version, '1.0.17+60') <= 0) {
            return 'Europe/Berlin';
        } else {
            return 'UTC';
        }
    }
}