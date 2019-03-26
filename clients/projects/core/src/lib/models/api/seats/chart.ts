import {Event} from './event';

export interface Chart {
  archived: boolean;
  draftVersionThumbnailUrl: string;
  events: Event[];
  id: number;
  key: string;
  name: string;
  publishedVersionThumbnailUrl: string;
  status: string;
  tags: [];
}
