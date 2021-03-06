<?php

namespace Drupal\unccd_event_map;

/**
 * Class EventStorage.
 */
class EventStorage {

    /**
     * Insert a new event into the database.
     *
     * @param array $entry An array containing all the fields of the database record.
     * @return int The number of updated rows.
     */
    public static function insert(array $entry) {
        $return_value = NULL;
        $return_value = db_insert('unccd_event_map')
            ->fields($entry)
            ->execute();
        return $return_value;
    }

    /**
     * Update an event already in the databasse.
     *
     * @param array $entry An array containing all the fields of the item to be updated.
     * @return int The number of updated rows.
     */
    public static function update(array $entry) {
        $count = db_update('unccd_event_map')
            ->fields($entry)
            ->condition('id', $entry['id'])
            ->execute();
        return $count;
    }

    /**
     * Delete an event from the database.
     *
     * @param int $id The id of the event to delete
     * @see db_delete()
     */
    public static function delete($id) {
        db_delete('unccd_event_map')
            ->condition('id', $id)
            ->execute();
    }

    public static function loadById($id) {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        $select->condition('id', $id);
        return $select->execute()->fetch();
    }

    /**
     * Retrieve all the events in the database.
     *
     * @return object An object containing the loaded entries if found.
     */
    public static function loadAll() {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        return $select->execute()->fetchAll();
    }

    /**
     * Retrieve all the events in the database paginated.
     * 
     * @param int $page The page to show
     * @return object An object containing the loaded entries if found.
     */
    public static function loadAllPaginated($page = 1) {
        $start = ($page - 1) * 50;
        $end = 50 * $page;
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        $select->orderBy('event_map.id', 'DESC');
        $select->range($start, $end);
        return $select->execute()->fetchAll();
    }
    
    /**
     * Retrieve all of the events matching provided conditions.
     *
     * @param array $entry An array containing all the fields used to search the entries in the table.
     * @return object An object containing the loaded entries if found.
     */
    public static function loadByCriteria(array $entry = []) {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');

        // Add each field and value as a condition to this query.
        foreach ($entry as $field => $value) {
            $select->condition($field, $value);
        }

        // Return the result in object format.
        return $select->execute()->fetchAll();
    }

    /**
     * Returns only the approved events.
     *
     * Equivalent SQL query:
     * SELECT
     *  e.id, e.title, e.city, e.country, e.latitude, e.longitude, e.approved
     * FROM
     *  {unccd_event_map} e
     * WHERE
     *  e.approved = 1
     */
    public static function loadApproved() {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        $select->condition('event_map.approved', 1);
        $select->orderBy('event_map.country', 'ASC');

        $entries = $select->execute()->fetchAll();

        return $entries;
    }

    /**
     * Returns only the approved events for a year.
     *
     * Equivalent SQL query:
     * SELECT
     *  e.id, e.title, e.city, e.country, e.latitude, e.longitude, e.approved
     * FROM
     *  {unccd_event_map} e
     * WHERE
     *  e.approved = 1
     * AND
     *  YEAR(e.date) = {year}
     */
    public static function loadApprovedInYear($year) {
        if($year == '') return self::loadApproved();
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        $select->condition('event_map.approved', 1);
        $select->where('YEAR(event_map.date) = :year', [':year' => $year]);
        $select->orderBy('event_map.country', 'ASC');

        $entries = $select->execute()->fetchAll();

        return $entries;
    }
}
