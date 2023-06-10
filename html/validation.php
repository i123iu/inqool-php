<?php

class Validation
{
    private DB $db;
    private Logger $logger;
    function __construct(DB $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }


    private function parse_int(string|null &$value): int|null
    {
        if (empty($value))
            return null;

        if (!is_numeric($value))
            return null;

        return intval($value);
    }

    public function parse_bool(bool|null &$bool): bool
    {
        if (empty($bool))
            return false;
        if ($bool == 'false')
            return false;
        if ($bool == 'true')
            return true;
        return boolval($bool);
    }

    public function parse_phone_number(string|null &$phone_number): string|null
    {
        if (empty($phone_number))
            return null;

        $s = str_replace(' ', '', $phone_number);

        if (!preg_match('/^\d{9}$/', $s))
            return null;

        return $s;
    }

    public function parse_date_time(string|null &$date_time): DateTime|null
    {
        if (empty($date_time))
            return null;

        $time = $this->db->parse_date_time($date_time);
        if ($time === false)
            return null;

        return $time;
    }



    public function validate_court_id(string|null $court_id): int|null
    {
        $value = $this->parse_int($court_id);
        if ($value === null) {
            $this->logger->log_error('invalid court_id');
            return null;
        }

        $a = $this->db->execute('SELECT 1 FROM courts WHERE id=:id', [':id' => [$value, PDO::PARAM_INT]]);
        if (count($a->fetchAll()) === 0) {
            $this->logger->log_error('no court with this court_id');
            return null;
        }

        return $value;
    }

    public function validate_reservation_id(string|null &$reservation_id): int|null
    {
        $value = $this->parse_int($reservation_id);
        if ($value === null) {
            $this->logger->log_error('invalid reservation_id');
            return null;
        }

        $a = $this->db->execute('SELECT 1 FROM reservations WHERE id=:id', [':id' => [$value, PDO::PARAM_INT]]);
        if (count($a->fetchAll()) === 0) {
            $this->logger->log_error('no reservation with this reservation_id');
            return null;
        }

        return $value;
    }

    public function validate_time_frame(int $court_id, DateTime $start_time, DateTime $end_time): bool
    {
        if ($start_time->getTimestamp() >= $end_time->getTimestamp()) {
            $this->logger->log_error('invalid time frame, end_time > start_time');
            return false;
        }

        // find reservations that collide with this time frame
        $a = $this->db->execute(
            'SELECT COUNT(*) as count FROM reservations WHERE court_id=:court_id AND DATETIME(:start_time)<DATETIME(end_time) AND DATETIME(:end_time)>DATETIME(start_time)',
            [
                ':court_id' => [$court_id, PDO::PARAM_INT],
                ':start_time' => [$this->db->format_date_time($start_time), PDO::PARAM_STR],
                ':end_time' => [$this->db->format_date_time($end_time), PDO::PARAM_STR],
            ]
        );

        $count = $a->fetchAll()[0]['count'];
        if ($count != 0) {
            $this->logger->log_error('invalid time frame, collides with a reservation');
            return false;
        }

        return true;
    }


    public function validate_reservation_post_data(): array|null
    {
        $court_id = $this->validate_court_id($_POST['court_id']);
        if ($court_id === null)
            return null;

        switch (strtolower($_POST['game_type'] ?? '')) {
            case 'single':
                $double_game = false;
                break;
            case 'double':
                $double_game = true;
                break;
            default:
                $this->logger->log_error('invalid game_type, can be \'single\' or \'double\'');
                return null;
        }

        $phone_number = $this->parse_phone_number($_POST['phone_number']);
        if ($phone_number === null) {
            $this->logger->log_error('invalid phone_number');
            return null;
        }

        $start_time = $this->parse_date_time($_POST['start_time']);
        if ($start_time === null) {
            $this->logger->log_error('invalid start_time');
            return null;
        }

        $end_time = $this->parse_date_time($_POST['end_time']);
        if ($end_time === null) {
            $this->logger->log_error('invalid end_time');
            return null;
        }

        if ($start_time < new DateTime('now') || $end_time < new DateTime('now')) {
            $this->logger->log_error('cannot book into the past');
            return null;
        }

        if ($end_time->getTimestamp() - $start_time->getTimestamp() > MAX_RESERVATION_DURATION * 60) {
            $this->logger->log_error('invalid duration, max is ' . MAX_RESERVATION_DURATION . ' minutes');
            return null;
        }

        if (!$this->validate_time_frame($court_id, $start_time, $end_time))
            return null;

        return [
            'court_id' => $court_id,
            'double_game' => $double_game,
            'phone_number' => $phone_number,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }

    public function validate_delete_post_data(): array|null
    {
        $reservation_id = $this->validate_reservation_id($_POST['reservation_id']);
        if ($reservation_id === null)
            return null;

        $phone_number = $this->parse_phone_number($_POST['phone_number']);
        if ($phone_number === null) {
            $this->logger->log_error('invalid phone_number');
            return null;
        }

        return [
            'reservation_id' => $reservation_id,
            'phone_number' => $phone_number,
        ];
    }
}