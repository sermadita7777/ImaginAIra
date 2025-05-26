/*
  # Initial Schema Setup for IA-Lovers

  1. New Tables
    - users
      - id (uuid, primary key)
      - username (text, unique)
      - created_at (timestamp)
    
    - content_items
      - id (uuid, primary key)
      - title (text)
      - description (text)
      - image_url (text)
      - category (text)
      - tag (text)
      - likes (integer)
      - created_by (uuid, references users)
      - created_at (timestamp)
    
    - likes
      - user_id (uuid, references users)
      - content_id (uuid, references content_items)
      - created_at (timestamp)
      - PRIMARY KEY (user_id, content_id)
    
    - cart_items
      - user_id (uuid, references users)
      - content_id (uuid, references content_items)
      - created_at (timestamp)
      - PRIMARY KEY (user_id, content_id)

  2. Security
    - Enable RLS on all tables
    - Add policies for authenticated users
*/

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id uuid PRIMARY KEY DEFAULT auth.uid(),
  username text UNIQUE NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE users ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read their own data"
  ON users
  FOR SELECT
  TO authenticated
  USING (auth.uid() = id);

-- Content items table
CREATE TABLE IF NOT EXISTS content_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  description text,
  image_url text,
  category text NOT NULL,
  tag text,
  likes integer DEFAULT 0,
  created_by uuid REFERENCES users(id),
  created_at timestamptz DEFAULT now()
);

ALTER TABLE content_items ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can read content"
  ON content_items
  FOR SELECT
  TO authenticated
  USING (true);

CREATE POLICY "Users can create content"
  ON content_items
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = created_by);

-- Likes table
CREATE TABLE IF NOT EXISTS likes (
  user_id uuid REFERENCES users(id),
  content_id uuid REFERENCES content_items(id),
  created_at timestamptz DEFAULT now(),
  PRIMARY KEY (user_id, content_id)
);

ALTER TABLE likes ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can manage their likes"
  ON likes
  FOR ALL
  TO authenticated
  USING (auth.uid() = user_id);

-- Cart items table
CREATE TABLE IF NOT EXISTS cart_items (
  user_id uuid REFERENCES users(id),
  content_id uuid REFERENCES content_items(id),
  created_at timestamptz DEFAULT now(),
  PRIMARY KEY (user_id, content_id)
);

ALTER TABLE cart_items ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can manage their cart"
  ON cart_items
  FOR ALL
  TO authenticated
  USING (auth.uid() = user_id);