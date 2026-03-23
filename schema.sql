-- =========================
-- TIMEZONE
-- =========================
SET TIME ZONE 'UTC';

-- =========================
-- ENUMS
-- =========================
CREATE TYPE product_type AS ENUM (
    'cpu',
    'gpu',
    'ram',
    'motherboard',
    'storage',
    'psu',
    'case',
    'cooler',
    'os'
);

-- =========================
-- BRANDS
-- =========================
CREATE TABLE brands (
    brand_id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    logo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- USERS (Admin merged)
-- =========================
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
        CHECK (role IN ('user','admin')),
    profile_image TEXT DEFAULT 'default.jpg',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- PRODUCTS (Unified + JSON specs)
-- =========================
CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    brand_id INTEGER REFERENCES brands(brand_id),
    type product_type NOT NULL,
    price NUMERIC(12,0) NOT NULL, -- VND only
    image TEXT,

    specs JSONB NOT NULL DEFAULT '{}'::jsonb,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- REVIEWS
-- =========================
CREATE TABLE reviews (
    review_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(product_id) ON DELETE CASCADE,
    rating INTEGER CHECK (rating BETWEEN 1 AND 5),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- COMMENTS (optional)
-- =========================
CREATE TABLE comments (
    comment_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(product_id) ON DELETE CASCADE,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- CART
-- =========================
CREATE TABLE cart_items (
    cart_item_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(product_id),
    quantity INTEGER NOT NULL DEFAULT 1
);

-- =========================
-- BUILDS (PC Builder)
-- =========================
CREATE TABLE builds (
    build_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    name TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE build_items (
    id SERIAL PRIMARY KEY,
    build_id INTEGER REFERENCES builds(build_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(product_id)
);

-- =========================
-- ORDERS
-- =========================
CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    total_amount NUMERIC(12,0),
    status VARCHAR(20) DEFAULT 'pending'
        CHECK (status IN ('pending','processed','shipped','delivered','cancelled')),
    payment_method VARCHAR(20)
        CHECK (payment_method IN ('Bank','COD')),
    payment_status VARCHAR(20) DEFAULT 'pending'
        CHECK (payment_status IN ('pending','paid','cancelled')),
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    order_item_id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES orders(order_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(product_id),
    quantity INTEGER NOT NULL,
    price_at_purchase NUMERIC(12,0) NOT NULL
);

-- =========================
-- INDEXES
-- =========================

-- Fast filtering
CREATE INDEX idx_products_type ON products(type);
CREATE INDEX idx_products_brand ON products(brand_id);

-- JSONB index (GIN for fast search within specs)
CREATE INDEX idx_products_specs ON products USING GIN (specs);

-- Reviews
CREATE INDEX idx_reviews_product ON reviews(product_id);

-- Orders
CREATE INDEX idx_orders_user ON orders(user_id);