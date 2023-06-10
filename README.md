# inqool-php

`docker-compose up`

Endpoints: 

 - `GET get-all-courts`: 
 - `GET get-reservations-by-court`: 
   - `court_id`
 - `POST get-reservations-by-phone-number`: 
   - `phone_number`
 - `POST make-reservation`: 
   - `court_id`, `game_type` (`single` or `double`), `phone_number`, `start_time` (`YYYY-MM-DD HH-MM`), `end_time`
 - `POST delete-reservation`: 
   - `reservation_id`, `phone_number`
