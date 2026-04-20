import { Component, isStandalone, OnDestroy, OnInit } from "@angular/core";

@Component({
  selector: 'app-admin',
  standalone: true,
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']

})
export class AdminComponent implements OnInit{

  ngOnInit(): void {
    throw new Error("Method not implemented.");
  }

}

