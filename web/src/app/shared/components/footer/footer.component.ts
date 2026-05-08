import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { SiteSettingsService } from '../../../util/site-settings.service';

@Component({
  selector: 'app-footer',
  imports: [CommonModule, RouterLink],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.css'
})
export class FooterComponent {
  constructor(public siteSettingsService: SiteSettingsService) {}
}
