import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';

@Component({
  selector: 'app-faq',
  imports: [LayoutComponent, CommonModule],
  templateUrl: './faq.component.html',
  styleUrl: './faq.component.css'
})
export class FaqComponent {
  activeFaq: number | null = null;

  toggleFaq(faqNumber: number) {
    this.activeFaq = this.activeFaq === faqNumber ? null : faqNumber;
  }
}
