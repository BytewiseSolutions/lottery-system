import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-settings',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.scss']
})
export class SettingsComponent {
  @Input() settingsTab = 'general';
  @Input() siteSettings: any = {};
  @Input() timezones: string[] = [];
  @Input() apiKeys: any[] = [];

  @Output() tabChange = new EventEmitter<string>();
  @Output() saveSettings = new EventEmitter<void>();
  @Output() generateApiKey = new EventEmitter<void>();
  @Output() copyApiKey = new EventEmitter<string>();
  @Output() revokeApiKey = new EventEmitter<number>();

  onTabChange(tab: string) {
    this.settingsTab = tab;
    this.tabChange.emit(tab);
  }

  onSaveSettings() {
    this.saveSettings.emit();
  }

  onGenerateApiKey() {
    this.generateApiKey.emit();
  }

  onCopyApiKey(key: string) {
    this.copyApiKey.emit(key);
  }

  onRevokeApiKey(id: number) {
    this.revokeApiKey.emit(id);
  }
}