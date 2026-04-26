ALTER TABLE banners ADD COLUMN IF NOT EXISTS media_type ENUM('image','video') DEFAULT 'image' AFTER image;
ALTER TABLE banners ADD COLUMN IF NOT EXISTS video_url VARCHAR(500) AFTER media_type;
