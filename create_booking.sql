
CREATE TABLE booking (
    bookingID INT AUTO_INCREMENT PRIMARY KEY,
    customerID INT NOT NULL,
    roomID INT NOT NULL,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    total_cost DECIMAL(10,2),
    booking_status VARCHAR(20) DEFAULT 'confirmed',
    special_requests TEXT,
    review_rating INT CHECK (review_rating >= 1 AND review_rating <= 5),
    review_comment TEXT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customerID) REFERENCES customer(customerID) ON DELETE CASCADE,
    FOREIGN KEY (roomID) REFERENCES room(roomID) ON DELETE RESTRICT
);
