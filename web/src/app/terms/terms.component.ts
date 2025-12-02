import { Component } from '@angular/core';
import { LayoutComponent } from '../layout/layout.component';

@Component({
  selector: 'app-terms',
  imports: [LayoutComponent],
  templateUrl: './terms.component.html',
  styles: [`
    .terms-container {
      padding: 20px;
      max-width: 800px;
      margin: 0 auto;
    }
  `]
})
export class TermsComponent {

}
