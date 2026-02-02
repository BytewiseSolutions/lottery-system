import { Injectable } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ShareService {
  async share(text: string, title: string = 'Share'): Promise<boolean> {
    if (navigator.share) {
      try {
        await navigator.share({ text, title });
        return true;
      } catch {
        return this.fallbackCopy(text);
      }
    }
    return this.fallbackCopy(text);
  }

  private fallbackCopy(text: string): boolean {
    try {
      navigator.clipboard.writeText(text);
      return true;
    } catch {
      return false;
    }
  }
}
