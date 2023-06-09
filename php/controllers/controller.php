<?php

require_once 'models/model.php';
require_once 'logger.php';
require_once 'validation.php';


class Controller
{
    private Model $model;
    private Logger $logger;
    private Validation $valid;
    function __construct(Model $model, Logger $logger)
    {
        $this->model = $model;
        $this->logger = $logger;
        $this->valid = new Validation($this->model->get_db(), $this->logger);
    }



    public function show_all_courts(): void
    {
        $res = $this->model->get_all_courts();
        $this->return_json($res);
    }

    public function show_reservations_by_court_id(): void
    {
        $court_id = $this->valid->validate_court_id($_GET['court_id']);
        if ($court_id === null) {
            $this->return_bad_request();
            return;
        }

        $res = $this->model->get_reservations_for_court($court_id);
        $this->return_json($res);
    }

    public function show_reservations_by_phone_number(): void
    {
        if (!$this->ensure_post_method())
            return;

        $phone_number = $this->valid->parse_phone_number($_POST['phone_number']);
        if ($phone_number === null) {
            $this->logger->log_error('invalid phone_number');
            $this->return_bad_request();
            return;
        }

        $res = $this->model->get_reservations_by_phone_number($phone_number);
        $this->return_json($res);
    }

    public function make_reservation(): void
    {
        if (!$this->ensure_post_method())
            return;

        $post_data = $this->valid->validate_reservation_post_data();
        if ($post_data === null) {
            $this->return_bad_request();
            return;
        }

        $reservation_id = $this->model->make_reservation(
            $post_data['court_id'], $post_data['double_game'], $post_data['phone_number'],
            $post_data['start_time'], $post_data['end_time']
        );
        if ($reservation_id === false){
            http_response_code(500);
            return;
        }

        // time in minutes
        $time_diff = (int) round(($post_data['end_time']->getTimeStamp() - $post_data['start_time']->getTimeStamp()) / 60);

        $price = $this->model->get_price($post_data['court_id']);
        $total_price = $price * $time_diff * ($post_data['double_game'] ? 1.5 : 1);

        $this->return_json(['price' => $total_price, 'reservation_id' => $reservation_id]);
    }

    public function delete_reservation(): void
    {
        if (!$this->ensure_post_method())
            return;

        $post_data = $this->valid->validate_delete_post_data();
        if ($post_data === null) {
            $this->return_bad_request();
            return;
        }

        $success = $this->model->delete_reservation($post_data['reservation_id'], $post_data['phone_number']);
        if ($success) {
            http_response_code(200);
            echo 'deleted';
        } else {
            $this->return_bad_request();
            echo 'not found';
        }
    }


    /**
     * @return bool True if in POST method. 
     */
    private function ensure_post_method(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo 'Use POST method';
            return false;
        }
        return true;
    }
    private function return_json($object)
    {
        header('Content-Type: application/json');
        echo json_encode($object, JSON_PRETTY_PRINT);
    }
    private function return_not_found()
    {
        http_response_code(404);
    }
    private function return_bad_request()
    {
        http_response_code(400);
        header('Content-Type: text/plain');
        echo $this->logger->get_all_errors();
    }
}