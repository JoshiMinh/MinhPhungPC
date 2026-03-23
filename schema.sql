-- PC Building Database Schema (PostgreSQL version)
-- Beautified, converted to PostgreSQL, no hardcoded values, no data inserts

-- Set timezone
SET TIME ZONE 'UTC';

-- Drop tables if they exist (for idempotency)
DROP TABLE IF EXISTS comments, orders, users, admin, cpucooler, graphicscard, memory, motherboard, operatingsystem, pccase, powersupply, processor, storage CASCADE;

-- Admin table
CREATE TABLE admin (
  admin_id SERIAL PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL
);

-- Users table
CREATE TABLE users (
  user_id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  date_of_birth DATE,
  profile_image VARCHAR(255) NOT NULL DEFAULT 'default.jpg',
  cart TEXT,
  buildset TEXT NOT NULL DEFAULT '',
  address TEXT
);

-- Comments table
CREATE TABLE comments (
  comment_id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(user_id),
  product_id VARCHAR(255),
  product_table VARCHAR(255),
  content TEXT,
  time TIMESTAMP
);

-- CPU Cooler table
CREATE TABLE cpucooler (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand VARCHAR(20),
  price INTEGER,
  image TEXT,
  cooling_type TEXT,
  socket TEXT,
  ratings TEXT
);

-- Graphics Card table
CREATE TABLE graphicscard (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand VARCHAR(20),
  vram_capacity INTEGER,
  cuda_cores INTEGER,
  tdp INTEGER,
  price INTEGER,
  image TEXT,
  ratings TEXT
);

-- Memory table
CREATE TABLE memory (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL,
  brand VARCHAR(255) NOT NULL,
  price INTEGER NOT NULL,
  image TEXT,
  ddr INTEGER,
  capacity VARCHAR(255),
  speed VARCHAR(255),
  ratings TEXT
);

-- Motherboard table
CREATE TABLE motherboard (
  id SERIAL PRIMARY KEY,
  brand VARCHAR(255),
  name VARCHAR(255),
  socket_type VARCHAR(50),
  chipset VARCHAR(50),
  memory_slots INTEGER,
  max_memory_capacity INTEGER,
  ddr VARCHAR(20),
  expansion_slots VARCHAR(20),
  price INTEGER,
  image TEXT,
  ratings TEXT
);

-- Operating System table
CREATE TABLE operatingsystem (
  id SERIAL PRIMARY KEY,
  name TEXT,
  version TEXT,
  price INTEGER,
  image TEXT,
  brand VARCHAR(20),
  ratings TEXT
);

-- Orders table
CREATE TABLE orders (
  order_id SERIAL PRIMARY KEY,
  customer_id INTEGER REFERENCES users(user_id),
  items TEXT,
  order_date TIMESTAMP,
  status VARCHAR(20) CHECK (status IN ('pending','processed','shipped','delivered','cancelled')),
  total_amount INTEGER,
  address VARCHAR(255),
  payment_method VARCHAR(10) CHECK (payment_method IN ('Bank','COD')),
  payment_status VARCHAR(10) CHECK (payment_status IN ('pending','paid','cancelled'))
);

-- PC Case table
CREATE TABLE pccase (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand VARCHAR(20),
  price INTEGER,
  image TEXT,
  size TEXT,
  ratings TEXT
);

-- Power Supply table
CREATE TABLE powersupply (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand TEXT,
  price INTEGER,
  image TEXT,
  wattage INTEGER,
  efficiency_rating TEXT,
  ratings TEXT
);

-- Processor table
CREATE TABLE processor (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand TEXT,
  price INTEGER,
  image TEXT,
  core_count INTEGER,
  thread_count INTEGER,
  socket_type TEXT,
  tdp INTEGER,
  ratings TEXT
);

-- Storage table
CREATE TABLE storage (
  id SERIAL PRIMARY KEY,
  name TEXT,
  brand TEXT,
  price INTEGER,
  image TEXT,
  type TEXT,
  capacity TEXT,
  speed TEXT,
  port TEXT,
  ratings TEXT
);

-- Indexes for foreign keys (optional, for performance)
CREATE INDEX idx_comments_user_id ON comments(user_id);
CREATE INDEX idx_orders_customer_id ON orders(customer_id);