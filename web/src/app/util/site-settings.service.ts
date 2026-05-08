import { Injectable, signal } from '@angular/core';
import { Title, Meta } from '@angular/platform-browser';
import { BackendService } from './backend.service';

interface RuntimeSettings {
  site_name: string;
}

@Injectable({
  providedIn: 'root'
})
export class SiteSettingsService {
  private initialized = false;
  private readonly defaultSiteName = 'Total Free Lotto';
  readonly siteName = signal(this.defaultSiteName);

  constructor(
    private backendService: BackendService,
    private title: Title,
    private meta: Meta
  ) {
    this.applyBranding(this.defaultSiteName);
  }

  init(): void {
    if (this.initialized) {
      return;
    }

    this.initialized = true;

    this.backendService.getSettings().subscribe({
      next: (response: any) => {
        const settings = response?.success ? (response.data as RuntimeSettings) : null;
        const nextSiteName = settings?.site_name?.trim() || this.defaultSiteName;
        this.siteName.set(nextSiteName);
        this.applyBranding(nextSiteName);
      },
      error: (error) => {
        console.error('Failed to load runtime site settings:', error);
        this.applyBranding(this.defaultSiteName);
      }
    });
  }

  private applyBranding(siteName: string): void {
    const pageTitle = `${siteName} - Play Free Lottery Games Online`;
    const pageDescription = `Play free lottery games online. Win prizes with ${siteName} - your trusted lottery platform.`;
    const shortDescription = `Play free lottery games online. Win prizes with ${siteName}.`;

    this.title.setTitle(pageTitle);
    this.meta.updateTag({ name: 'description', content: pageDescription });
    this.meta.updateTag({ name: 'author', content: siteName });
    this.meta.updateTag({ property: 'og:title', content: pageTitle });
    this.meta.updateTag({ property: 'og:description', content: shortDescription });
    this.meta.updateTag({ name: 'twitter:title', content: pageTitle });
    this.meta.updateTag({ name: 'twitter:description', content: shortDescription });

    const schemaScript = document.querySelector('script[type="application/ld+json"]');
    if (schemaScript?.textContent) {
      try {
        const schema = JSON.parse(schemaScript.textContent);
        schema.name = siteName;
        schema.description = shortDescription;
        schemaScript.textContent = JSON.stringify(schema, null, 2);
      } catch (error) {
        console.error('Failed to update schema.org branding metadata:', error);
      }
    }
  }
}
