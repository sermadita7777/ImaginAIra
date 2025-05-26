/*
  # Initial Schema Setup

  1. New Tables
    - users: Store user information
    - content_items: Store content like memes, art, and literature
    - likes: Track user likes on content
    - cart_items: Track items in user carts

  2. Security
    - Enable RLS on all tables
    - Add appropriate policies for each table
    - Ensure users can only access their own data

  3. Features
    - Automatic likes counter using triggers
    - Foreign key relationships for data integrity
*/

-- Drop existing objects if they exist
DROP TRIGGER IF EXISTS update_content_likes_count ON likes;
DROP FUNCTION IF EXISTS update_likes_count();
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS content_items;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  username text UNIQUE NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE users ENABLE ROW LEVEL SECURITY;

DO $$ BEGIN
  CREATE POLICY "Users can read their own data"
    ON users
    FOR SELECT
    TO authenticated
    USING (auth.uid() = id);
EXCEPTION
  WHEN duplicate_object THEN NULL;
END $$;

-- Content items table
CREATE TABLE content_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  description text,
  image_url text,
  category text NOT NULL,
  tag text,
  likes_count integer DEFAULT 0,
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
CREATE TABLE likes (
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
CREATE TABLE cart_items (
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

-- Trigger to update likes count
CREATE OR REPLACE FUNCTION update_likes_count()
RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
    UPDATE content_items
    SET likes_count = likes_count + 1
    WHERE id = NEW.content_id;
  ELSIF TG_OP = 'DELETE' THEN
    UPDATE content_items
    SET likes_count = likes_count - 1
    WHERE id = OLD.content_id;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_content_likes_count
AFTER INSERT OR DELETE ON likes
FOR EACH ROW
EXECUTE FUNCTION update_likes_count();