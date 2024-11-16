CREATE TABLE cities (   -- Fixed table name from 'cites' to 'cities'
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    college_name VARCHAR(100) NOT NULL,
    PRIMARY KEY(id)
    profile_picture VARCHAR(255) DEFAULT NULL,      
);

CREATE TABLE properties (
    id INT NOT NULL AUTO_INCREMENT,
    city_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    gender ENUM('male', 'female', 'unisex') NOT NULL,
    rent INT NOT NULL,
    rating_clean DECIMAL(3,1) NOT NULL,  -- Changed to DECIMAL for precision
    rating_food DECIMAL(3,1) NOT NULL,   -- Changed to DECIMAL for precision
    rating_safety DECIMAL(3,1) NOT NULL, -- Changed to DECIMAL for precision
    PRIMARY KEY(id),
    FOREIGN KEY (city_id) REFERENCES cities(id)   -- Ensure 'cities' is correct
);

CREATE TABLE amenities (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    icon VARCHAR(30) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE properties_amenities (
    id INT NOT NULL AUTO_INCREMENT,
    property_id INT NOT NULL,
    amenity_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (amenity_id) REFERENCES amenities(id)
);

CREATE TABLE testimonials (
    id INT NOT NULL AUTO_INCREMENT,
    property_id INT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (property_id) REFERENCES properties(id)
);

CREATE TABLE interested_users_properties (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id)
);
