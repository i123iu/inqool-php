<?php

require_once 'models/db.php';
require_once 'logger.php';

class Model
{
    private DB $db;
    private Logger $logger;
    function __construct(DB $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get_db(): DB
    {
        return $this->db;
    }


    public function get_all_courts() : array
    {
        $a = $this->db->execute(
            'SELECT courts.id as court_id, courts.name as court_name, surfaces.id as surface_id, surfaces.name as surface_name, surfaces.price as price
                FROM courts, surfaces
                WHERE surface_id = surfaces.id'
        );
        return $a->fetchAll();
    }

    public function get_reservations_for_court(int $court_id) : array
    {
        $a = $this->db->execute(
            'SELECT id, court_id, double_game, start_time, end_time FROM reservations WHERE court_id=:id AND start_time',
            [':id' => [$court_id, PDO::PARAM_INT]]
        );
        return $a->fetchAll();
    }

    public function get_reservations_by_phone_number(string $phone_number) : array
    {
        $a = $this->db->execute(
            'SELECT * FROM reservations WHERE phone_number=:phone_number',
            [':phone_number' => [$phone_number, PDO::PARAM_STR]]
        );
        return $a->fetchAll();
    }

    public function get_price(int $court_id): float|null
    {
        $a = $this->db->execute(
            'SELECT surfaces.price FROM courts, surfaces WHERE courts.surface_id=surfaces.id AND courts.id=:court_id LIMIT 1',
            [':court_id' => [$court_id, PDO::PARAM_STR]]
        );
        $res = $a->fetchAll();
        if (count($res) === 0)
            return null;
        return $res[0]['price'];
    }

    public function make_reservation(int $court_id, bool $double_game, string $phone_number, DateTime $start_time, DateTime $end_time): int|bool
    {
        $a = $this->db->execute(
            'INSERT INTO reservations (court_id, phone_number, double_game, start_time, end_time) VALUES (:court_id, :phone_number, :double_game, :start_time, :end_time)',
            [
                ':court_id' => $court_id,
                ':phone_number' => $phone_number,
                ':double_game' => $double_game,
                ':start_time' => $this->db->format_date_time($start_time),
                ':end_time' => $this->db->format_date_time($end_time)
            ]
        );
        return $this->db->get_last_insert_id();
    }

    /**
     * @return bool True if deleted
     */
    public function delete_reservation(int $reservation_id, string $phone_number) : bool
    {
        $a = $this->db->execute(
            'DELETE FROM reservations WHERE id=:id AND phone_number=:phone_number',
            [
                ':id' => [$reservation_id, PDO::PARAM_INT],
                ':phone_number' => [$phone_number, PDO::PARAM_STR],
            ]
        );

        return $a->rowCount() > 0;
    }
}