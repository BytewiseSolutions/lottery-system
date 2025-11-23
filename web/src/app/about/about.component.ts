import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';

@Component({
  selector: 'app-about',
  imports: [RouterLink, LayoutComponent],
  templateUrl: './about.component.html',
  styleUrl: './about.component.css'
})
export class AboutComponent {

}
