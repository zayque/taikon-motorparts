-- ----------------------------------------------------------------------
-- Taikon Motorparts — database schema (MySQL 8 / MariaDB 10.6+)
-- Import order: schema.sql, then seed.sql.
-- ----------------------------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS login_attempts, messages, bookings, services, order_items, orders, products, categories, users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL,
    phone          VARCHAR(20)  NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    address_line   VARCHAR(255) NULL,
    city           VARCHAR(80)  NULL,
    postcode       VARCHAR(10)  NULL,
    state          VARCHAR(80)  NULL,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name         VARCHAR(80)  NOT NULL,
    slug         VARCHAR(100) NOT NULL,
    description  VARCHAR(255) NULL,
    icon         VARCHAR(30)  NOT NULL DEFAULT 'box',
    sort_order   SMALLINT     NOT NULL DEFAULT 0,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id    INT UNSIGNED NOT NULL,
    name           VARCHAR(150) NOT NULL,
    slug           VARCHAR(170) NOT NULL,
    sku            VARCHAR(40)  NOT NULL,
    brand          VARCHAR(60)  NULL,
    fits           VARCHAR(255) NULL COMMENT 'Compatible models, free text',
    description    TEXT NULL,
    price          DECIMAL(10,2) NOT NULL,
    compare_price  DECIMAL(10,2) NULL COMMENT 'Original price when on sale',
    stock          INT NOT NULL DEFAULT 0,
    image          VARCHAR(120) NULL,
    is_featured    TINYINT(1) NOT NULL DEFAULT 0,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY products_slug_unique (slug),
    UNIQUE KEY products_sku_unique (sku),
    KEY products_category_active (category_id, is_active),
    FULLTEXT KEY products_search (name, brand, fits, description),
    CONSTRAINT products_category_fk FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_number    VARCHAR(20)  NOT NULL,
    user_id         INT UNSIGNED NULL,
    customer_name   VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    fulfilment      ENUM('delivery', 'pickup') NOT NULL DEFAULT 'delivery',
    address_line    VARCHAR(255) NULL,
    city            VARCHAR(80)  NULL,
    postcode        VARCHAR(10)  NULL,
    state           VARCHAR(80)  NULL,
    payment_method  ENUM('cod', 'bank_transfer', 'pay_at_store') NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    shipping_fee    DECIMAL(10,2) NOT NULL DEFAULT 0,
    total           DECIMAL(10,2) NOT NULL,
    status          ENUM('pending', 'processing', 'shipped', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    notes           VARCHAR(500) NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY orders_number_unique (order_number),
    KEY orders_user (user_id),
    KEY orders_status (status),
    CONSTRAINT orders_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id      INT UNSIGNED NOT NULL,
    product_id    INT UNSIGNED NULL,
    product_name  VARCHAR(150) NOT NULL,
    sku           VARCHAR(40)  NOT NULL,
    unit_price    DECIMAL(10,2) NOT NULL,
    quantity      INT UNSIGNED NOT NULL,
    line_total    DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY order_items_order (order_id),
    CONSTRAINT order_items_order_fk FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT order_items_product_fk FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name              VARCHAR(120) NOT NULL,
    slug              VARCHAR(140) NOT NULL,
    summary           VARCHAR(255) NOT NULL,
    description       TEXT NULL,
    features          TEXT NULL COMMENT 'One feature per line',
    price_from        DECIMAL(10,2) NOT NULL,
    duration_minutes  SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    icon              VARCHAR(30) NOT NULL DEFAULT 'wrench',
    is_package        TINYINT(1) NOT NULL DEFAULT 0,
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    sort_order        SMALLINT NOT NULL DEFAULT 0,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY services_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    booking_number  VARCHAR(20)  NOT NULL,
    user_id         INT UNSIGNED NULL,
    service_id      INT UNSIGNED NULL,
    service_name    VARCHAR(120) NOT NULL,
    customer_name   VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    motorcycle      VARCHAR(120) NOT NULL,
    plate_number    VARCHAR(15)  NULL,
    booking_date    DATE NOT NULL,
    booking_time    TIME NOT NULL,
    notes           VARCHAR(1000) NULL,
    status          ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY bookings_number_unique (booking_number),
    KEY bookings_slot (booking_date, booking_time, status),
    KEY bookings_user (user_id),
    CONSTRAINT bookings_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT bookings_service_fk FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20)  NULL,
    subject     VARCHAR(150) NOT NULL,
    body        TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY messages_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email         VARCHAR(150) NOT NULL,
    ip_address    VARCHAR(45)  NOT NULL,
    attempted_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY login_attempts_lookup (email, ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
