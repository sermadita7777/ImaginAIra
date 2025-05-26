/*
  # Initial Schema Setup

  1. Tables
    - users: Store user information
    - content_items: Store memes, art, and literature content
    - likes: Track user likes on content
    - cart_items: Track items in user carts

  2. Security
    - Enable RLS on all tables
    - Add appropriate policies for each table
*/

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
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