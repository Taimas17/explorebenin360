export type MediaType = 'image' | 'video' | 'panorama' | 'document';

export interface MediaItem {
  id?: number;
  model_type?: string | null;
  model_id?: number | null;
  type: MediaType;
  url: string;
  provider: 'cloudinary' | 's3' | 'local';
  alt?: string;
  caption?: string;
  width?: number | null;
  height?: number | null;
  size_bytes?: number | null;
  mime?: string | null;
  metadata_json?: Record<string, any> | null;
  created_by?: number | null;
  created_at?: string;
  updated_at?: string;
}
