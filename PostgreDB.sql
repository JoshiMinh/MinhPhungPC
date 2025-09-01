-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Categories
CREATE TABLE Category (
  category_id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  display_name VARCHAR(255) NOT NULL
);

-- Insert initial categories
INSERT INTO Category (name, display_name) VALUES
  ('gpu', 'Graphic Cards'),
  ('ram', 'RAM'),
  ('motherboard', 'Motherboard'),
  ('os', 'OS'),
  ('pccase', 'Case'),
  ('power', 'Power Supply'),
  ('cpu', 'CPU'),
  ('storage', 'Storage'),
  ('cpucooler', 'Cooler');

-- ENUM Types
CREATE TYPE order_status AS ENUM ('pending', 'cancelled', 'shipped');
CREATE TYPE payment_method AS ENUM ('COD', 'Bank');
CREATE TYPE payment_status AS ENUM ('pending', 'paid', 'cancelled');

-- Products
CREATE TABLE products (
  product_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  category_id INT REFERENCES Category(category_id),
  username TEXT,
  brand VARCHAR(255),
  price INT,
  image TEXT,
  ratings TEXT,
  attributes JSONB
);

-- Users
CREATE TABLE users (
  user_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  admin BOOLEAN DEFAULT FALSE,
  date_of_birth DATE,
  profile_image VARCHAR(255) DEFAULT 'default.jpg',
  cart TEXT,
  buildset TEXT NOT NULL,
  address TEXT
);

-- Comments
CREATE TABLE comments (
  comment_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID REFERENCES users(user_id),
  product_id UUID REFERENCES products(product_id),
  content TEXT,
  time TIMESTAMP
);

-- Orders
CREATE TABLE orders (
  order_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID REFERENCES users(user_id),
  items TEXT NOT NULL,
  order_date TIMESTAMP NOT NULL,
  status order_status,
  total_amount NUMERIC(15, 2) NOT NULL,
  address TEXT NOT NULL,
  payment_method payment_method NOT NULL,
  payment_status payment_status
);