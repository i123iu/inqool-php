<?php

require_once 'models/model.php';
require_once 'logger.php';
require_once 'controllers/controller.php';

class Router
{
    private Model $model;
    private Logger $logger;
    function __construct(Model $model, Logger $logger)
    {
        $this->model = $model;
        $this->logger = $logger;
    }

    private function parse_uri(string $request_uri): string
    {
        // remove /
        $page = substr($request_uri, 1);
        // remove .php
        if (str_ends_with($page, '.php'))
            $page = substr($page, 0, -strlen('.php'));
        // delete get query
        $page = parse_url($page, PHP_URL_PATH);
        return $page;
    }

    public function process_request(string $req): void
    {
        $path = $this->parse_uri($req);
        $contr = new Controller($this->model, $this->logger);

        switch ($path) {
            case 'get-all-courts':
                $contr->show_all_courts();
                break;

            case 'get-reservations-by-court':
                $contr->show_reservations_by_court_id();
                break;

            case 'get-reservations-by-phone-number':
                $contr->show_reservations_by_phone_number();
                break;

            case 'make-reservation':
                $contr->make_reservation();
                break;

            case 'delete-reservation':
                $contr->delete_reservation();
                break;

            default:
                http_response_code(404);
                break;
        }

    }
}