


SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS flights;
DROP TABLE IF EXISTS airlines;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,            
    full_name     VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    address       VARCHAR(255),
    contact_no    VARCHAR(20),
    user_type     VARCHAR(10)  NOT NULL DEFAULT 'customer',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (user_type IN ('customer','admin'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE airlines (
    airline_id    INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE flights (
    flight_id     INT AUTO_INCREMENT PRIMARY KEY,
    flight_no     VARCHAR(10)  NOT NULL UNIQUE,
    flight_name   VARCHAR(100) NOT NULL,
    source        VARCHAR(100) NOT NULL,
    destination   VARCHAR(100) NOT NULL,
    flight_date   DATE         NOT NULL,
    flight_time   TIME         NOT NULL,
    airline_id    INT,
    total_seats   INT          NOT NULL DEFAULT 180,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (airline_id) REFERENCES airlines(airline_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE classes (
    class_id      INT AUTO_INCREMENT PRIMARY KEY,
    flight_id     INT NOT NULL,
    class_type    VARCHAR(20) NOT NULL,
    fare          DECIMAL(10,2) NOT NULL,
    seats_available INT NOT NULL DEFAULT 60,
    UNIQUE KEY uniq_flight_class (flight_id, class_type),
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id) ON DELETE CASCADE,
    CHECK (class_type IN ('economy','premium economy','business'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE tickets (
    ticket_id         INT AUTO_INCREMENT PRIMARY KEY,
    pnr               VARCHAR(10)  NOT NULL UNIQUE,
    flight_id         INT          NOT NULL,
    user_id           INT          NOT NULL,
    class_type        VARCHAR(20)  NOT NULL,
    passenger_name    VARCHAR(100) NOT NULL,
    age               INT          NOT NULL,
    gender            VARCHAR(10)  NOT NULL,
    meal_choice       BOOLEAN      NOT NULL DEFAULT FALSE,
    lounge_access     BOOLEAN      NOT NULL DEFAULT FALSE,
    priority_checkin  BOOLEAN      NOT NULL DEFAULT FALSE,
    insurance         BOOLEAN      NOT NULL DEFAULT FALSE,
    no_of_passengers  INT          NOT NULL DEFAULT 1,
    fare_amount       DECIMAL(10,2) NOT NULL,
    booking_datetime  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status            VARCHAR(15)  NOT NULL DEFAULT 'CONFIRMED',
    cancelled_at      TIMESTAMP    NULL DEFAULT NULL,
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CHECK (status IN ('CONFIRMED','CANCELLED'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE payments (
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id      INT NOT NULL,
    transaction_id VARCHAR(30) NOT NULL UNIQUE,
    amount         DECIMAL(10,2) NOT NULL,
    payment_mode   VARCHAR(20) NOT NULL,
    payment_date   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status         VARCHAR(15) NOT NULL DEFAULT 'SUCCESS',
    FOREIGN KEY (ticket_id) REFERENCES tickets(ticket_id) ON DELETE CASCADE,
    CHECK (status IN ('SUCCESS','REFUNDED'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO users (username, password, full_name, email, address, contact_no, user_type)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator',
        'admin@getair.com', 'GetAir HQ', '9999999999', 'admin');

INSERT INTO airlines (name) VALUES ('GetAir'), ('GetAir Express'), ('GetAir Regional');

INSERT INTO flights (flight_no, flight_name, source, destination, flight_date, flight_time, airline_id, total_seats)
VALUES
('AA106','GetAir 106','New York','London', CURDATE() + INTERVAL 3 DAY, '09:30:00', 1, 180),
('AA210','GetAir 210','Mumbai','Dubai',   CURDATE() + INTERVAL 2 DAY, '14:15:00', 1, 160),
('AA315','GetAir 315','Delhi','Singapore',CURDATE() + INTERVAL 5 DAY, '22:00:00', 2, 200),
('AA420','GetAir 420','Pune','Bangalore', CURDATE() + INTERVAL 1 DAY, '07:45:00', 3, 120);

INSERT INTO classes (flight_id, class_type, fare, seats_available) VALUES
(1,'economy', 350.00, 120),(1,'premium economy', 650.00, 30),(1,'business', 1200.00, 30),
(2,'economy', 180.00, 100),(2,'premium economy', 320.00, 30),(2,'business', 600.00, 30),
(3,'economy', 420.00, 140),(3,'premium economy', 780.00, 30),(3,'business', 1500.00, 30),
(4,'economy',  60.00,  90),(4,'premium economy', 110.00, 15),(4,'business', 220.00, 15);
