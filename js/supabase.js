import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL;
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY;

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

// Auth functions
export async function signUp(email, password) {
  const { data, error } = await supabase.auth.signUp({
    email,
    password
  });
  return { data, error };
}

export async function signIn(email, password) {
  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password
  });
  return { data, error };
}

export async function signOut() {
  const { error } = await supabase.auth.signOut();
  return { error };
}

// Content functions
export async function getContentByCategory(category) {
  const { data, error } = await supabase
    .from('content_items')
    .select('*, likes(count)')
    .eq('category', category);
  return { data, error };
}

export async function addToLikes(contentId) {
  const { data, error } = await supabase
    .from('likes')
    .insert([{ content_id: contentId, user_id: supabase.auth.user()?.id }]);
  return { data, error };
}

export async function removeLike(contentId) {
  const { data, error } = await supabase
    .from('likes')
    .delete()
    .match({ content_id: contentId, user_id: supabase.auth.user()?.id });
  return { data, error };
}

export async function addToCart(contentId) {
  const { data, error } = await supabase
    .from('cart_items')
    .insert([{ content_id: contentId, user_id: supabase.auth.user()?.id }]);
  return { data, error };
}

export async function removeFromCart(contentId) {
  const { data, error } = await supabase
    .from('cart_items')
    .delete()
    .match({ content_id: contentId, user_id: supabase.auth.user()?.id });
  return { data, error };
}

export async function getUserLikes() {
  const { data, error } = await supabase
    .from('likes')
    .select(`
      content_items (
        id,
        title,
        description,
        image_url,
        category,
        likes
      )
    `)
    .eq('user_id', supabase.auth.user()?.id);
  return { data, error };
}

export async function getUserCart() {
  const { data, error } = await supabase
    .from('cart_items')
    .select(`
      content_items (
        id,
        title,
        description,
        image_url,
        category
      )
    `)
    .eq('user_id', supabase.auth.user()?.id);
  return { data, error };
}